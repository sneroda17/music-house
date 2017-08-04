@extends('index')
@section('meta-content')
	<meta name="twitter:card" value="photo"/>
	<meta name="twitter:site" value="{{ '@'.$settings->twitter_page_id }}"/>
	<meta name="twitter:image" value="{{ URL::asset('assets/images/logo.png') }}"/>
	<meta property="og:type" content="blog"/>
	<meta property="og:site_name" content="{{ $settings->website_name }}"/>
	<meta property="og:url" content="{{ Request::url() }}"/>
	<meta property="og:title" content="{{ $settings->website_name }} - {{ Lang::get('words.my-favorite-tracks') }}"/>
	<meta property="og:image" content="{{ URL::asset('assets/images/logo.png') }}"/>
	<meta property="og:description" content="{{ $settings->website_name }} - {{ Lang::get('words.my-favorite-tracks') }}"/>
	<title>{{ $settings->website_name }} - {{ Lang::get('words.my-favorite-tracks') }}</title>
@stop

@section('content')
<?php
	$tl_counts = DB::table('track_likes')->whereIn('track_id', $trackLikes->lists('track_id'))->select(DB::raw('track_id, count(id) AS count'))->groupBy('track_id')->get();
	$tl_array = array();
	foreach($tl_counts as $tl_count) {
		$tl_array[$tl_count->track_id] = $tl_count->count;
	}
	$downloadLink = '';
	if($settings->downloadable) {
		$downloadLink = '<button title="'.Lang::get("words.download").'" class="btn btn-transparent btn-circle track-download"><span class="fa fa-download"></span></button>';
	}
?>

<div class="row">
	<div class="col-xs-12">
		<div class="hidden-xs text-center" style="width:728px;margin:15px auto 0">
			<iframe src="{{ URL::to('ads/banner') }}" width='728' height="90" frameborder='0' border='0' marginwidth='0' marginheight='0' scrolling='no'></iframe>
		</div>
		<h2><span class="text-muted">{{ Lang::get('words.favorite') }}</span> <a class="fav-link active pjax" href="{{ URL::to('favorites') }}" title="{{ Lang::get('words.tracks') }}">{{ Lang::get('words.tracks') }}</a> <a class="fav-link pjax" href="{{ URL::to('favorites/albums') }}" title="{{ Lang::get('words.albums') }}">{{ Lang::get('words.albums') }}</a></h2>
		<br />
	</div>
</div>
<div class="row">
	@if($trackLikes->count())
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
				@foreach($trackLikes as $tracklike)
					<?php
						$track = $tracklike->track; 
						$track_likes = isset($tl_array[$track->id]) ? $tl_array[$track->id] : 1;
						$likepercent = Custom::popularity($track_likes);
						$likepercent = $likepercent < 10 ? 10 : $likepercent;
					?>
					<tr class="scroll-items" data-title="{{ $track->title }}" data-trackid="{{ $track->id }}" data-artist="{{ $track->artist->name }}" data-slug="{{ $track->slug }}" data-track="{{ URL::to('uploads/audios/'.$track->location.'/'.$track->slug.'.mp3') }}" data-album_title="{{ $track->album->title }}" data-cover="{{ URL::to('uploads/albums/'.$track->album->location.'/thumb/'.$track->album->slug.'.jpg') }}">
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
							<button title="{{ Lang::get('words.favorite') }}" class="btn btn-transparent btn-circle track-like active"><span class="fa fa-heart"></span></button>
						</td>
					</tr>
				@endforeach
				</tbody>
			</table>
		</div>
		{{ $trackLikes->links('pagination::simple') }}
	@else
		<div class="col-xs-12">
			<div class="alert alert-danger"><i class="fa fa-meh-o"></i> Sorry, No Tracks found</div>
		</div>
	@endif
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

@stop