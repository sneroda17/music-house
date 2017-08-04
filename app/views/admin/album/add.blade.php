@extends('admin.index')
@section('meta-content')
<title>{{ Lang::get('words.admin_alt').' - '.Lang::get('words.add-track') }}</title>
@stop
@section('content')
	<div class="row">
		<div class="col-xs-12">
			<ol class="breadcrumb">
				<li><a href="{{ URL::to('admin') }}">{{ Lang::get('words.admin') }}</a></li>
				<li><a href="{{ URL::to('admin/album') }}">{{ Lang::get('words.albums') }}</a></li>
				<li><a href="{{ URL::to('admin/album/'.$album->slug) }}">{{ $album->title }}</a></li>
				<li class="active">{{ Lang::get('words.add-track') }}</li>
			</ol>
		</div>
	</div>
    <form id="add-track" role="form" method="post" action="{{ URL::to('admin/album/'.$album->slug.'/add') }}" enctype="multipart/form-data">
        <div class="row">
        	<div class="col-sm-5">
        		<div class="panel panel-default">
        			<div class="panel-heading">
        				<h4 style="margin:0">{{ $album->title }}</h4>
        			</div>
        			<div class="panel-body text-muted">
						<div class="media">
							<div class="media-left">
								<img class="img-responsive" src="{{ URL::to('uploads/albums/'.$album->location.'/thumb/'.$album->slug.'.jpg') }}">
							</div>
							<div class="media-right">
								<p>{{ Lang::get('words.artist') }}: <a title="{{ $album->artist->name }} {{ Lang::get('words.albums') }}" class="pjax" href="{{ URL::to('admin/artist/'.$album->artist->slug) }}">{{ $album->artist->name }}</a></p>
								<p>{{ Lang::get('words.category') }}: <a title="{{ $album->category->name }} {{ Lang::get('words.albums') }}" class="pjax" href="{{ URL::to('admin/category/'.$album->category->slug) }}">{{ $album->category->name }}</a></p>
								<p>{{ Lang::get('words.language') }}: <a title="{{ $album->language->name }} {{ Lang::get('words.albums') }}" class="pjax" href="{{ URL::to('admin/language/'.$album->language->slug) }}">{{ $album->language->name }}</a></p>
								<p>{{ Lang::get('words.views') }}: <span class="text-danger">{{ $album->views }}</span></p>
								<p>{{ Lang::get('words.date') }}: {{ $album->created_at->toFormattedDateString() }}</p>
								<p>{{ Lang::get('words.released') }}: {{ $album->release_date }}</p>
							</div>
						</div>
					</div>
				</div>		
			</div>
            <div class="col-sm-7">
            	<div class="form-group">
            		<div class="btn-group btn-block">
					<span class="btn btn-primary btn-file" style="width:50%">
						{{ Lang::get('words.select-mp3') }}
						<input type="file" class="btn" id="sl-audio" name="audio" multiple>
					</span>
					<button id="upload-mp3s" class="btn btn-danger" disabled="disabled" style="width:50%">{{ Lang::get('words.upload-mp3') }}</button>
					</div>
				</div>
				<div class="form-group">
					<div class="progress hidden" id="add-track-pbar">
	  					<div class="progress-bar progress-bar-striped progress-bar-success active" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
	  				</div>
				</div>
                <div id="sel-tracks" style="max-height:250px; overflow-x:hidden"></div>
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<div class="table-responsive">
					<table class="table">
						<h2 class="text-muted">{{ Lang::get('words.album') }} {{ Lang::get('words.tracks') }} ({{ $album->tracks->count() }})</h2>
						<tr>
							<th>Sr.</th>
							<th>{{ Lang::get('words.title') }}</th>
							<th>{{ Lang::get('words.artist') }}</th>
							<th>{{ Lang::get('words.duration') }}</th>
							<th>{{ Lang::get('words.size') }}</th>
							<th>{{ Lang::get('words.downloads') }}</th>
							<th class="text-center">{{ Lang::get('words.actions') }}</th>
						</tr>
						@foreach($album->tracks as $i => $track)
						<tr>
							<td>{{ $i+1 }}</td>
							<td>{{ $track->title }}</td>
							<td>{{ $track->artist->name }}</td>
							<td>{{ Custom::formatMilliseconds($track->duration) }}</td>
							<td>{{ Custom::formatBytes($track->filesize) }}</td>
							<td>{{ $track->downloads }}</td>
							<td class="text-right">
								<div class="btn-group">
									<a data-title="{{ $track->title }}" data-artist="{{ $track->artist->name }}" data-slug="{{ $track->slug }}" data-toggle="modal" data-target="#edit-track-modal" class="btn btn-xs btn-primary pjax"><i class="fa fa-pencil"></i> {{ Lang::get('words.edit') }}</a>
									<a href="{{ URL::to('admin/track/'.$track->slug.'/delete') }}" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i> {{ Lang::get('words.delete') }}</a>
								</div>
							</td>
						</tr>
						@endforeach
					</table>
				</div>
			</div>
		</div>
   </form>
<div class="modal fade" id="edit-track-modal" tabindex="-1" role="dialog" aria-labelledby="deleteProductModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<form id="edit-track-form" role="form" method="post" action="{{ URL::to('admin/track/edit') }}" enctype="multipart/form-data">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">{{ Lang::get('words.edit') }} {{ Lang::get('words.track') }}</h4>
				</div>
				<div class="modal-body">
					<input type="hidden" name="slug" value="">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<div class="form-group">
						<label>{{ Lang::get('words.track') }} {{ Lang::get('words.name') }}:</label>
						<input type="text" class="form-control" name="title" placeholder="{{ Lang::get('words.name') }}">
					</div>
					<div class="form-group">
						<label>{{ Lang::get('words.track') }} {{ Lang::get('words.artist') }}:</label>
						<input type="text" class="typeahead form-control" data-role="tagsinput" name="artist" placeholder="artist">
					</div>
				</div>
				<div class="modal-footer">
					<input type="submit" class="btn btn-danger btn-block" value="{{ Lang::get('words.save') }} {{ Lang::get('words.track') }}">
				</div>
			</form>
		</div>
	</div>
</div>
<div class="track-info hidden">
<div class="form-group">
    <div class="row">
    	<div class="col-xs-6">
			<input type="text" class="form-control audio-title" name="title" placeholder="{{ Lang::get('words.track') }} {{ Lang::get('words.title') }}">
		</div>
		<div class="col-xs-6">
			<input type="text" class="artist-tags form-control" value="{{ $album->artist->name }}" name="artist" placeholder="{{ Lang::get('words.artist') }} {{ Lang::get('words.name') }}">
		</div>
		<div class="clearfix"></div>
	</div>
</div>
</div>
@stop