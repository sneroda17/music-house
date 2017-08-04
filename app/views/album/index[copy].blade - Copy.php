@extends('index')
@section('meta-content')
	<?php $albumImg =  URL::to('uploads/albums/'.$album->location.'/'.$album->slug.'.jpg'); ?>
	<meta name="twitter:card" value="photo"/>
	<meta name="twitter:site" value="{{ '@'.$settings->twitter_page_id }}"/>
	<meta name="twitter:image" value="{{ $albumImg }}"/>
	<meta property="og:type" content="article" />
	<meta property="og:site_name" content="{{ $settings->website_name }}"/>
	<meta property="og:url" content="{{ Request::url() }}"/>
	<meta property="og:title" content="{{ Lang::get('words.download') }} {{ $album->title }} {{ Lang::get('words.tracks') }}"/>
	<meta property="og:image" content="{{ $albumImg }}"/>
	<meta property="og:description" content="{{ Lang::get('words.download') }} {{ $album->title }} {{ Lang::get('words.tracks') }}"/>
	<title>{{ Lang::get('words.download') }} {{ $album->title }} {{ Lang::get('words.tracks') }}</title>
@stop

@section('content')
<?php
	$tracks = $album->tracks()->with('artist', 'is_favorite','category')->paginate(20);
	$tl_counts = $album->getPopularity($tracks->lists('id'))->get();
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
	<div class="transparent-img" style="background-image:url('{{ URL::to('uploads/albums/'.$album->location.'/thumb/'.$album->slug.'.jpg') }}')"></div>
	<div class="album-header">
		<div class="media">
			<div class="media-left play-al-cont hidden-480">
				<img src="{{ URL::to('uploads/albums/'.$album->location.'/thumb/'.$album->slug.'.jpg') }}">
				<div class="play-album" data-album="{{ $album->slug }}">
            		<span class="fa fa-play"></span>
            	</div>
			</div>
			<div class="play-album visible-480" data-album="{{ $album->slug }}">
        		<span class="fa fa-play"></span>
        	</div>
			<div class="media-body">
				<h1 class="media-heading">{{ $album->title }}</h1>
				<p>
					<span class="text-muted">{{ Lang::get('words.length') }}:</span> {{ Custom::formatMilliseconds($tracks->sum('duration')) }} ({{ $tracks->count() }} Tracks) | <span class="text-muted">Released:</span> {{ $album->release_date }}
				</p>
				<p>
					<span class="text-muted">{{ Lang::get('words.size') }}:</span> {{ Custom::formatBytes($tracks->sum('filesize')) }}
				</p>
				<p>
					<span class="text-muted">{{ Lang::get('words.artist') }}:</span> <a class="link-grey" href="{{ URL::to('artist/'.$album->artist->slug) }}"> {{ $album->artist->name }}</a>
				</p>
				<p>
					<span class="text-muted">{{ Lang::get('words.favorites') }}:</span> <span id="al-fav-count">{{ $album->likes() }}</span>
				</p>
				@if($settings->zip_download)<button class="btn btn-labeled btn-primary album-download" data-album="{{ $album->slug }}"><span class="btn-label"><i class="fa fa-download"></i></span> {{ Lang::get('words.download') }}</button>@endif
				<div class="album-actions" data-album="{{ $album->id }}">
					<button class="btn btn-transparent btn-circle album-like @if($album->is_favorite->count()) active @endif"><span class="fa fa-heart"></span></button>
					<button data-target="#social-share-modal" data-toggle="modal" class="btn btn-transparent btn-circle"><span class="fa fa-share-alt"></span></button>
				</div>
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
						<button title="{{ Lang::get('words.add-to-playlist') }}" class="btn btn-transparent btn-circle" data-toggle="modal" data-target="#track-playlist-modal"><span class="fa fa-plus"></span></button>
						<button title="{{ Lang::get('words.favorite') }}" class="btn btn-transparent btn-circle track-like @if($track->is_favorite->count()) active @endif"><span class="fa fa-heart"></span></button>
					</td>
				</tr>
			@endforeach
			</tbody>
			{{ $tracks->links('pagination::simple') }}
		</table>
		<div class="hidden-xs text-center" style="width:728px;margin:15px auto 0">
			<iframe src="{{ URL::to('ads/banner') }}" width='728' height="90" frameborder='0' border='0' marginwidth='0' marginheight='0' scrolling='no'></iframe>
		</div>
	</div>
</div>

@if(!Auth::guest())

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
		            <div class="funkyradio" style="height:320px; overflow-y:scroll">
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
@endif

<div class="modal fade" id="social-share-modal" tabindex="-1" role="dialog" aria-labelledby="social-share-modalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="social-share-modalLabel"><i class="fa fa-share-alt"></i> {{ Lang::get('words.share') }}</h4>
      </div>
      <div class="modal-body">
    	<div class="ft-social text-center">
			<a target="_blank" href="http://www.facebook.com/sharer.php?u={{ Request::url() }}" title="" class="btn btn-circle bg-facebook"><span class="fa fa-facebook"></span></a>
			<a target="_blank" href="https://twitter.com/share?url={{ Request::url() }}&text={{ Lang::get('words.download').' '.$album->title }}&via={{ $settings->twitter_page_id }}" title="" class="btn btn-circle bg-twitter"><span class="fa fa-twitter"></span></a>
			<a target="_blank" href="https://plus.google.com/share?url={{ Request::url() }}" title="" class="btn btn-circle bg-google"><span class="fa fa-google-plus"></span></a>
			<a target="_blank" href="http://www.linkedin.com/shareArticle?url={{ Request::url() }}&title={{ Lang::get('words.download').' '.$album->title }}" title="" class="btn btn-circle bg-linkedin"><span class="fa fa-linkedin"></span></a>
			<a target="_blank" href="http://digg.com/submit?url={{ Request::url() }}&title={{ Lang::get('words.download').' '.$album->title }}" title="" class="btn btn-circle bg-digg"><span class="fa fa-digg"></span></a>
			<a target="_blank" href="http://www.stumbleupon.com/submit?url={{ Request::url() }}&title={{ Lang::get('words.download').' '.$album->title }}" title="" class="btn btn-circle bg-stumble"><span class="fa fa-stumbleupon"></span></a>
			<a target="_blank" href="https://pinterest.com/pin/create/bookmarklet/?media={{ $albumImg }}&url={{ Request::url() }}&description={{ Lang::get('words.download').' '.$album->title }}" title="" class="btn btn-circle bg-pinterest"><span class="fa fa-pinterest"></span></a>
			<a target="_blank" href="http://vk.com/share.php?url={{ Request::url() }}" title="" class="btn btn-circle bg-vk"><span class="fa fa-vk"></span></a>
		</div>
      </div>
    </div>
  </div>
</div>

@stop