@extends('admin.index')
@section('meta-content')
<title>{{ Lang::get('words.admin_alt').' - '.Lang::get('words.artists') }}</title>
@stop
@section('content')
<?php
	$aa_counts = DB::table('albums')->whereIn('artist_id', $artists->lists('id'))->select(DB::raw('artist_id, count(id) AS count'))->groupBy('artist_id')->get();
	$aa_array = array();
	foreach($aa_counts as $aa_count) {
		$aa_array[$aa_count->artist_id] = $aa_count->count;
	}
	$at_counts = DB::table('tracks')->whereIn('artist_id', $artists->lists('id'))->select(DB::raw('artist_id, count(id) AS count'))->groupBy('artist_id')->get();
	$at_array = array();
	foreach($at_counts as $at_count) {
		$at_array[$at_count->artist_id] = $at_count->count;
	}
?>
	<div class="row">
		<div class="col-xs-12">
			<ol class="breadcrumb">
				<li><a href="{{ URL::to('admin') }}">{{ Lang::get('words.admin') }}</a></li>
				<li class="active">{{ Lang::get('words.artists') }} (<b>{{ DB::table("artists")->count() }}</b>)</li>
			</ol>
		</div>
	</div>
	<div class="row">
		<form method="get">
			<div class="col-xs-12">
				<div class="form-group input-group">
					<input type="text" class="form-control" name="q" value="@if(Input::get('q')){{ Input::get('q') }}@endif" placeholder="{{ Lang::get('words.search') }}" aria-describedby="basic-addon2">
					<div class="input-group-btn">
						<button type="submit" class="btn btn-primary"><span class="fa fa-search"></span> {{ Lang::get('words.search') }}</button>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
		</form>
	</div>
	<div class="row">
		@foreach($artists as $artist)
			<?php
				$album_count = isset($aa_array[$artist->id]) ? $aa_array[$artist->id] : 0;
				$tracks_count = isset($at_array[$artist->id]) ? $at_array[$artist->id] : 0;
			?>
			<div class="col-lg-3 col-md-4 col-sm-6" style="margin-bottom:15px">
				<div class="panel panel-default">
					<div class="panel-heading text-nowrap" style="padding:10px;">
						<h5 style="margin:0">{{ $artist->name }} ({{ $album_count }})</h5>
					</div>
					<div class="panel-body" style="padding:10px;">
					<div class="row">
						<div class="col-xs-6">
							<p>
							<a href="{{ URL::to('admin/artist/'.$artist->slug) }}">
			    				<img class="img-responsive" src="@if($artist->image) {{ URL::to('uploads/artists/'.$artist->image.'/'.$artist->slug.'.jpg') }} @else {{ URL::asset('assets/images/default.jpg') }} @endif">
			    			</a>
			    			</p>
						</div>
						<div class="col-xs-6">
							<div class="row text-muted">
								<p>{{ Lang::get('words.artist') }}: <a title="{{ $artist->name }} {{ Lang::get('words.albums') }}" class="pjax" href="{{ URL::to('admin/artist/'.$artist->slug) }}">{{ $artist->name }}</a></p>
								<p>{{ Lang::get('words.albums') }}: {{ $album_count }}</p>
								<p>{{ Lang::get('words.tracks') }}: {{ $tracks_count }}</p>
							</div>
						</div>
						<div class="clearfix"></div>
					</div>
					</div>
					<div class="panel-footer">
						<a data-name="{{ $artist->name }}" data-id="{{ $artist->id }}" data-toggle="modal" data-target="#edit-artist-modal" class="btn btn-sm btn-info btn-block"><i class="fa fa-pencil"></i> {{ Lang::get('words.edit') }}</a>
					</div>
				</div>
			</div>
		@endforeach
   </div>
   <div class="row">
	    <div class="col-xs-12 text-center">
	        {{ $artists->links() }}
	    </div>
	</div>
<div class="modal fade" id="edit-artist-modal" tabindex="-1" role="dialog" aria-labelledby="deleteProductModalLabel" aria-hidden="true">
	<div class="modal-dialog">
    	<div class="modal-content">
      		<div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        <h4 class="modal-title">{{ Lang::get('words.edit-user') }}</h4>
      		</div>
      		<form id="edit-artist-form" role="form" method="post" action="{{ URL::to('admin/artist') }}" enctype="multipart/form-data">
	      		<div class="modal-body">
			    	<input type="hidden" name="id" value="">
			    	<input type="hidden" name="_token" value="{{ csrf_token() }}">
			    	<input type="hidden" name="imgurl" value="">
		            <div class="form-group">
						<label>{{ Lang::get('words.artist') }} {{ Lang::get('words.name') }}:</label>
						<input type="text" class="form-control" name="title" placeholder="{{ Lang::get('words.name') }}">
					</div>
					<div class="form-group">
						<label>{{ Lang::get('words.artist-image') }}:</label>
						<div class="al-art-opt">
							<span class="btn btn-file btn-primary">
								<input type="file" name="image">
								{{ Lang::get('words.browse-image') }}
							</span>
							OR
							<input class="btn btn-info" id="getImgUrl" value="Get via Web" />
						</div>
					</div>
	      		</div>
	      		<div class="modal-footer">
	        		<input type="submit" class="btn btn-danger btn-block" value="{{ Lang::get('words.save') }} {{ Lang::get('words.artist') }}">
	      		</div>
	      	</form>
    	</div>
	</div>
</div>
@stop

