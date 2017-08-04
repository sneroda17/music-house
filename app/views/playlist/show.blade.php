@extends('index')
@section('meta-content')
	<meta name="twitter:card" value="photo"/>
	<meta name="twitter:site" value="{{ '@'.$settings->twitter_page_id }}"/>
	<meta name="twitter:image" value="{{ URL::asset('assets/images/logo.png') }}"/>
	<meta property="og:type" content="blog"/>
	<meta property="og:site_name" content="{{ $settings->website_name }}"/>
	<meta property="og:url" content="{{ Request::url() }}"/>
	<meta property="og:title" content="{{ Lang::get('words.playlist') }}: {{ $playlist->title }}"/>
	<meta property="og:image" content="{{ URL::asset('assets/images/logo.png') }}"/>
	<meta property="og:description" content="{{ $settings->website_name }} - {{ Lang::get('words.playlist') }}: {{ $playlist->title }}"/>
	<title>{{ Lang::get('words.playlist') }}: {{ $playlist->title }}</title>
@stop

@section('content')
<?php
	$tracks = $playlist->tracks()->with('artist', 'is_favorite')->paginate(20);
	//dd($tracks->toArray());
	$tl_counts = $playlist->getPopularity($tracks->lists('id'))->get();
	$tl_array = array();
	foreach($tl_counts as $tl_count) {
		$tl_array[$tl_count->track_id] = $tl_count->count;
	}
	$downloadLink = '';
	if($settings->downloadable) {
		$downloadLink = '<button title="'.Lang::get("words.download").'" class="btn btn-transparent btn-circle track-download"><span class="fa fa-download"></span></button>';
	}
?>
<div class="row bg-transparent">
	<div class="transparent-img" style="background-image:url('{{ URL::asset('assets/images/artwork.jpg') }}')"></div>
	<div class="album-header">
		<div class="media">
			<div class="media-left play-al-cont hidden-480">
				<img src="{{ URL::asset('assets/images/artwork.jpg') }}" alt="{{ $playlist->title }}" class="img-responsive">
		        <div class="play-album" data-playlist="{{ $playlist->slug }}">
            		<span class="fa fa-play"></span>
            	</div>
			</div>
			<div class="play-album visible-480" data-playlist="{{ $playlist->slug }}">
        		<span class="fa fa-play"></span>
        	</div>
			<div class="media-body">
				<h1 class="media-heading">{{ $playlist->title }} </h1>
				<p>
					<span class="text-muted">{{ Lang::get('words.length') }}:</span> {{ Custom::formatMilliseconds($tracks->sum('duration')) }} ({{ $tracks->count() }} {{ Lang::get('words.tracks') }})
				</p>
				<p>
					<span class="text-muted">{{ Lang::get('words.size') }}:</span> {{ Custom::formatBytes($tracks->sum('filesize')) }}
				</p>
				<p>
					<span class="text-muted">{{ Lang::get('words.created-on') }}:</span> {{ $playlist->created_at->toFormattedDateString(); }}
				</p>
				<p>
					<button class="btn btn-labeled btn-primary" data-toggle="modal" data-target="#edit-playlist-modal"><span class="btn-label"><i class="fa fa-edit"></i></span> {{ Lang::get('words.edit') }}</button>
					&nbsp;&nbsp;&nbsp;&nbsp;
					<button class="btn btn-labeled btn-danger" data-toggle="modal" data-target="#rm-playlist-modal"><span class="btn-label"><i class="fa fa-trash"></i></span> {{ Lang::get('words.delete') }}</button>
				</p>
			</div>
		</div>
	</div>
	<div class="hidden-xs text-center" style="width:728px;margin:15px auto 0">
		<iframe src="{{ URL::to('ads/banner') }}" width='728' height="90" frameborder='0' border='0' marginwidth='0' marginheight='0' scrolling='no'></iframe>
	</div>
	<div class="table-responsive">
		<table class="table album-tracks">
			<thead>
				<tr>
					<th>{{ Lang::get('words.title') }}</th>
					<th class="hidden-480">{{ Lang::get('words.duration') }}</th>
					<th class="hidden-480">{{ Lang::get('words.size') }}</th>
					<th class="hidden-480">{{ Lang::get('words.popularity') }}</th>
					<th class="text-center">{{ Lang::get('words.actions') }}</th>
				</tr>
			</thead>
			<tbody class="scroll-container">
			@foreach($tracks as $track)
				<?php
					$track_likes = isset($tl_array[$track->id]) ? $tl_array[$track->id] : 1;
					$likepercent = Custom::popularity($track_likes);
					$likepercent = $likepercent < 10 ? 10 : $likepercent;
				?>
				<tr class="scroll-items" data-title="{{ $track->title }}" data-trackid="{{ $track->id }}" data-artist="{{ $track->artist->name }}" data-slug="{{ $track->slug }}" data-track="{{ URL::to('uploads/audios/'.$track->location.'/'.$track->slug.'.mp3') }}">
					<td>
						<button class="btn btn-transparent btn-circle pull-left track-play">
							<span class="fa fa-play"></span>
						</button>
						<div class="track-album pull-left">
							<a class="tt-title" title="{{ Lang::get('words.download').' '.$track->title }}" href="{{ URL::to('track/'.Custom::slugify($track->title).'-'.$track->slug) }}">{{ $track->title }}</a>
							<a class="tt-artist" title="{{ Lang::get('words.browse-albums', array('title' => $track->artist->name)) }}" href="{{ URL::to('artist/'.$track->artist->slug) }}">{{ $track->artist->name }}</a>
						</div>
						<div class="clearfix"></div>
					</td>
					<td class="hidden-480">
						{{ Custom::formatMilliseconds($track->duration) }}
					</td>
					<td class="hidden-480">
						{{ Custom::formatBytes($track->filesize) }}
					</td>
					<td class="hidden-480">
						<span class="rating-wrap">
							<span class="rating-bar" style="width:{{ $likepercent }}%"></span>
						</span>
					</td>
					<td class="text-center">
						{{ $downloadLink }}
						<button title="{{ Lang::get('words.remove-track') }}" class="btn btn-transparent btn-circle" data-toggle="modal" data-target="#rm-track-modal"><span class="fa fa-trash"></span></button>
						<button title="{{ Lang::get('words.add-to-playlist') }}" class="btn btn-transparent btn-circle" data-toggle="modal" data-target="#track-playlist-modal"><span class="fa fa-plus"></span></button>
						<button title="{{ Lang::get('words.favorite') }}" class="btn btn-transparent btn-circle track-like @if($track->is_favorite->count()) active @endif"><span class="fa fa-heart"></span></button>
					</td>
				</tr>
			@endforeach
			</tbody>
		</table>
	</div>
	{{ $tracks->links('pagination::simple') }}
</div>
<div class="modal fade" id="rm-playlist-modal" tabindex="-1" role="dialog" aria-labelledby="deleteProductModalLabel" aria-hidden="true">
	<div class="modal-dialog">
    	<div class="modal-content">
      		<div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        <h4 class="modal-title">{{ Lang::get('words.delete-playlist') }}</h4>
      		</div>
      		<form id="rm-playlist-form" role="form" method="post" action="{{ URL::to('playlist/'.$playlist->slug.'/delete') }}">
	      		<div class="modal-body">
			    	<input type="hidden" name="_token" value="{{ csrf_token() }}">
			    	<h5>{{ Lang::get('words.delete-playlist-msg') }}</h5>
	      		</div>
	      		<div class="modal-footer">
	        		<input type="submit" class="btn btn-danger btn-block" value="{{ Lang::get('words.delete-playlist') }}">
	      		</div>
	      	</form>
    	</div>
	</div>
</div>
<div class="modal fade" id="rm-track-modal" tabindex="-1" role="dialog" aria-labelledby="deleteProductModalLabel" aria-hidden="true">
	<div class="modal-dialog">
    	<div class="modal-content">
      		<div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        <h4 class="modal-title">{{ Lang::get('words.remove-track') }}</h4>
      		</div>
      		<form id="rm-track-form" role="form" method="post" action="{{ URL::to('playlist/'.$playlist->id.'/track/remove') }}">
	      		<div class="modal-body">
			    	<input type="hidden" name="_token" value="{{ csrf_token() }}">
			    	<input type="hidden" name="playlist" value="{{ $playlist->slug }}">
			    	<input type="hidden" name="track" value="">
			    	<h5>{{ Lang::get('words.remove-track-msg') }}</h5>
	      		</div>
	      		<div class="modal-footer">
	        		<input type="submit" class="btn btn-danger btn-block" value="{{ Lang::get('words.remove-track') }}">
	      		</div>
	      	</form>
    	</div>
	</div>
</div>
<div class="modal fade" id="edit-playlist-modal" tabindex="-1" role="dialog" aria-labelledby="deleteProductModalLabel" aria-hidden="true">
	<div class="modal-dialog">
    	<div class="modal-content">
      		<div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        <h4 class="modal-title">{{ Lang::get('words.edit-playlist') }}</h4>
      		</div>
      		<form id="edit-playlist-form" role="form" method="post" action="{{ URL::to('playlist/'.$playlist->slug.'/edit') }}">
	      		<div class="modal-body">
			    	<input type="hidden" name="_token" value="{{ csrf_token() }}">
			    	<div class="form-group">
			    		<label>{{ Lang::get('words.title') }}: </label>
			    		<input type="text" placeholder="{{ Lang::get('words.title') }}" class="form-control" value="{{ $playlist->title }}" name="title">
			    	</div>		            
	      		</div>
	      		<div class="modal-footer">
	        		<input type="submit" class="btn btn-danger btn-block" value="{{ Lang::get('words.save-playlist') }}">
	      		</div>
	      	</form>
    	</div>
	</div>
</div>
<?php $playlists = Playlist::where('user_id', Auth::user()->id)->select(array('id','title'))->take(50)->get(); ?>
<div class="modal fade" id="track-playlist-modal" tabindex="-1" role="dialog" aria-labelledby="deleteProductModalLabel" aria-hidden="true">
	<div class="modal-dialog">
    	<div class="modal-content">
      		<div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        <h4 class="modal-title">{{ Lang::get('words.add-to-playlist') }}</h4>
      		</div>
      		<form id="track-playlist-form" role="form" method="post" action="{{ URL::to('playlist/track/add') }}">
	      		<div class="modal-body">
			    	<input type="hidden" name="_token" value="{{ csrf_token() }}">
			    	<input type="hidden" name="track" value="">
		            <div class="funkyradio">
		            	@foreach($playlists as $i => $playlistm)
		            		<div class="funkyradio-success">
					            <input type="radio" name="playlist" value="{{ $playlistm->id }}" id="radio{{ $i }}" />
					            <label for="radio{{ $i }}">{{ $playlistm->title }}</label>
					        </div>
		            	@endforeach
		            </div>
	      		</div>
	      		<div class="modal-footer">
	        		<input type="submit" class="btn btn-danger btn-block" value="{{ Lang::get('words.add-track') }}">
	      		</div>
	      	</form>
    	</div>
	</div>
</div>
@stop