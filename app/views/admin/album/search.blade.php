@extends('admin.index')
@section('meta-content')
<title>{{ Lang::get('words.admin_alt').' - '.$ptitle }}</title>
@stop
@section('content')
<?php
	$albums = $data->albums()->paginate(20);
	$aa_counts = DB::table('tracks')->whereIn('album_id', $albums->lists('id'))->select(DB::raw('album_id, count(id) AS count'))->groupBy('album_id')->get();
	$aa_array = array();
	foreach($aa_counts as $aa_count) {
		$aa_array[$aa_count->album_id] = $aa_count->count;
	}
?>
	<div class="row">
		<div class="col-xs-12">
			<ol class="breadcrumb">
				<li><a href="{{ URL::to('admin') }}">{{ Lang::get('words.admin') }}</a></li>
				<li><a href="{{ URL::to('admin/albums') }}">{{ Lang::get('words.albums') }}</a></li>
				<li class="active">{{ $data->name }}</li>
			</ol>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<h2 style="margin-top:0">{{ $ptitle }} ({{ $data->albums->count() }})</h2>
		</div>
	</div>
	<div class="row">
		@forelse($albums as $album)
			<?php $tracks_count = isset($aa_array[$album->id]) ? $aa_array[$album->id] : 0; ?>
			<div class="col-lg-3 col-md-4 col-sm-6" style="margin-bottom:15px">
				<div class="panel panel-default">
					<div class="panel-heading text-nowrap" style="padding:10px;">
						<h5 style="margin:0">{{ $album->title }} ({{ $tracks_count }})</h5>
					</div>
					<div class="panel-body" style="padding:10px;">
					<div class="row">
						<div class="col-xs-6">
							<p>
							<a title="{{ $album->title }}" href="{{ URL::to('admin/album/'.$album->slug) }}">
			    				<img alt="{{ $album->title }}" class="img-responsive" src="{{ URL::to('uploads/albums/'.$album->location.'/thumb/'.$album->slug.'.jpg') }}">
			    			</a>
			    			</p>
						</div>
						<div class="col-xs-6">
							<div class="row text-muted">
							<p>{{ Lang::get('words.artist') }}: <a title="{{ $album->artist->name }} {{ Lang::get('words.albums') }}" class="pjax" href="{{ URL::to('admin/artist/'.$album->artist->slug) }}">{{ $album->artist->name }}</a></p>
							<p>{{ Lang::get('words.category') }}: <a title="{{ $album->category->name }} {{ Lang::get('words.albums') }}" class="pjax" href="{{ URL::to('admin/category/'.$album->category->slug) }}">{{ $album->category->name }}</a></p>
							<p>{{ Lang::get('words.language') }}: <a title="{{ $album->language->name }} {{ Lang::get('words.albums') }}" class="pjax" href="{{ URL::to('admin/language/'.$album->language->slug) }}">{{ $album->language->name }}</a></p>
							<p>{{ Lang::get('words.views') }}: <span class="text-danger">{{ $album->views }}</span></p>
							<p>{{ Lang::get('words.date') }}: {{ $album->created_at->toFormattedDateString() }}</p>
							<p>{{ Lang::get('words.released') }}: {{ $album->release_date }}</p>
							</div>
						</div>
						<div class="clearfix"></div>
					</div>
					</div>
					<div class="panel-footer" style="padding:10px;">
						<div class="btn-group">
							<a href="{{ URL::to('admin/album/'.$album->slug.'/add') }}" class="btn btn-sm btn-primary pjax"><i class="fa fa-plus"></i> {{ Lang::get('words.add-track') }}</a>
							<a data-title="{{ $album->title }}" data-category="{{ $album->category->name }}" data-artist="{{ $album->artist->name }}" data-language="{{ $album->language->name }}" data-release="{{ $album->release_date }}" data-slug="{{ $album->slug }}" data-toggle="modal" data-target="#edit-album-modal" class="btn btn-sm btn-info"><i class="fa fa-pencil"></i> {{ Lang::get('words.edit') }}</a>
							<a href="{{ URL::to('admin/album/'.$album->slug.'/delete') }}" class="btn btn-sm btn-danger pjax"><i class="fa fa-trash"></i> {{ Lang::get('words.delete') }}</a>
						</div>
					</div>
				</div>
			</div>
		@empty
		<div class="col-xs-12">
			<div class="alert alert-danger">No Album to Show</div>
		</div>
		@endforelse
   </div>
   <div class="row">
	    <div class="col-xs-12 text-center">
	        {{ $albums->links() }}
	    </div>
	</div>
<div class="modal fade" id="edit-album-modal" tabindex="-1" role="dialog" aria-labelledby="deleteProductModalLabel" aria-hidden="true">
	<div class="modal-dialog">
    	<div class="modal-content">
      		<div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        <h4 class="modal-title">{{ Lang::get('words.edit') }} {{ Lang::get('words.album') }}</h4>
      		</div>
      		<form id="edit-album-form" role="form" method="post" action="{{ URL::to('admin/album/edit') }}" enctype="multipart/form-data">
	      		<div class="modal-body">
			    	<input type="hidden" name="slug" value="">
			    	<input type="hidden" name="_token" value="{{ csrf_token() }}">
		            <div class="form-group">
						<label>{{ Lang::get('words.album') }} {{ Lang::get('words.name') }}:</label>
						<input type="text" class="form-control" name="title" placeholder="{{ Lang::get('words.album') }} {{ Lang::get('words.name') }}">
					</div>
					<div class="form-group">
						<label>{{ Lang::get('words.album') }} {{ Lang::get('words.artist') }}:</label>
						<input type="text" class="typeahead" name="artist" placeholder="{{ Lang::get('words.artist') }}">
					</div>
					<div class="form-group">
						<label>{{ Lang::get('words.album') }} {{ Lang::get('words.category') }}:</label>
						<input type="text" class="typeahead2" name="category" placeholder="{{ Lang::get('words.category') }}">
					</div>
					<div class="form-group">
						<label>{{ Lang::get('words.album') }} {{ Lang::get('words.language') }}:</label>
						<input type="text" class="typeahead3" name="language" placeholder="{{ Lang::get('words.language') }}">
					</div>
					<div class="form-group">
						<label>{{ Lang::get('words.album') }} {{ Lang::get('words.cover') }}</label>
						<span class="btn btn-file btn-primary">
							<input type="file" name="image">
							{{ Lang::get('words.browse-image') }}
						</span>
					</div>
					<div class="form-group">
						<label>{{ Lang::get('words.released') }}:</label>
						<input type="text" class="form-control datepicker" data-date-format="yyyy-mm-dd" name="release" placeholder="DD/MM/YYYY">
					</div>
	      		</div>
	      		<div class="modal-footer">
	        		<input type="submit" class="btn btn-danger btn-block" value="{{ Lang::get('words.save') }} {{ Lang::get('words.album') }}">
	      		</div>
	      	</form>
    	</div>
	</div>
</div>
<div class="modal fade" id="add-album-modal" tabindex="-1" role="dialog" aria-labelledby="deleteProductModalLabel" aria-hidden="true">
	<div class="modal-dialog">
    	<div class="modal-content">
    	    <form id="create-album" role="form" method="post" action="{{ URL::to('admin/album/create') }}" enctype="multipart/form-data">
      		<div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        <h4 class="modal-title">{{ Lang::get('words.add-new') }}</h4>
      		</div>
      		<div class="modal-body">
		    	<input type="hidden" name="_token" value="{{ csrf_token() }}">
	            <div class="form-group">
					<label>{{ Lang::get('words.album') }} {{ Lang::get('words.name') }}:</label>
					<input type="text" class="form-control" name="title" placeholder="{{ Lang::get('words.album') }} {{ Lang::get('words.name') }}">
				</div>
				<div class="form-group">
					<label>{{ Lang::get('words.album') }} {{ Lang::get('words.artist') }}:</label>
					<input type="text" class="typeahead" name="artist" placeholder="{{ Lang::get('words.artist') }}">
				</div>
				<div class="form-group">
					<label>{{ Lang::get('words.album') }} {{ Lang::get('words.category') }}:</label>
					<input type="text" class="typeahead2" name="category" placeholder="{{ Lang::get('words.category') }}">
				</div>
				<div class="form-group">
					<label>{{ Lang::get('words.album') }} {{ Lang::get('words.language') }}:</label>
					<input type="text" class="typeahead3" name="language" placeholder="{{ Lang::get('words.language') }}">
				</div>
				<div class="form-group">
					<label>{{ Lang::get('words.album') }} {{ Lang::get('words.cover') }}</label>
					<span class="btn btn-file btn-primary">
						<input type="file" name="image">
						{{ Lang::get('words.browse-image') }}
					</span>
				</div>
				<div class="form-group">
					<label>{{ Lang::get('words.released') }}:</label>
					<input type="text" class="form-control datepicker" data-date-format="yyyy-mm-dd" name="release" placeholder="DD/MM/YYYY">
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-block btn-danger">{{ Lang::get('words.create') }}</button>
			</div>
			</form>
    	</div>
	</div>
</div>
<script type="text/javascript">
		$('.datepicker').datepicker({
			todayHighlight: true,
			autoclose: true
		});
	</script>
@stop

