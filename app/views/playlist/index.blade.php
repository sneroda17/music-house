@extends('index')
@section('meta-content')
	<meta name="twitter:card" value="photo"/>
	<meta name="twitter:site" value="{{ '@'.$settings->twitter_page_id }}"/>
	<meta name="twitter:image" value="{{ URL::asset('assets/images/logo.png') }}"/>
	<meta property="og:type" content="blog"/>
	<meta property="og:site_name" content="{{ $settings->website_name }}"/>
	<meta property="og:url" content="{{ Request::url() }}"/>
	<meta property="og:title" content="{{ $settings->website_name }} - {{ Lang::get('words.my-playlists') }}"/>
	<meta property="og:image" content="{{ URL::asset('assets/images/logo.png') }}"/>
	<meta property="og:description" content="{{ $settings->website_name }} - {{ Lang::get('words.my-playlists') }}"/>
	<title>{{ $settings->website_name }} - {{ Lang::get('words.my-playlists') }}</title>
@stop

@section('content')
<?php
	$aa_counts = DB::table('playlist_tracks')->whereIn('playlist_id', $playlists->lists('id'))->select(DB::raw('playlist_id, count(id) AS count'))->groupBy('playlist_id')->get();
	$aa_array = array();
	foreach($aa_counts as $aa_count) {
		$aa_array[$aa_count->playlist_id] = $aa_count->count;
	}
?>
<div class="row">
	<div style="padding:0 15px">
		<div class="hidden-xs text-center" style="width:728px;margin:15px auto 0">
			<iframe src="{{ URL::to('ads/banner') }}" width='728' height="90" frameborder='0' border='0' marginwidth='0' marginheight='0' scrolling='no'></iframe>
		</div>
		<h2 class="text-muted">{{ Lang::get('words.my-playlists') }} ({{ Auth::user()->playlistsCount() }})</h2>
	    <br>
		<div class="col-lg-2 col-md-3 col-sm-4 col-xs-4" style="padding:0; margin-bottom:15px">
			<div class="album">
	    		<div class="bg-transparent">
	    			<div class="transparent-img"></div>
		    		<a class="text-center create-playlist" data-toggle="modal" data-target="#create-playlist-modal"  style="display:block;position:relative; padding:5px">
		        		<p>
		        			<span class="fa fa-5x fa-plus-square-o"></span>
		        		</p>
		        		<p class="fa fa-2x">
		        			{{ Lang::get('words.create-playlist') }}
		        		</p>
		        	</a>
	        	</div>
	        	
	       	</div>
	    </div>
	    <div class="scroll-container">
			@foreach($playlists as $playlist)
			<?php $tracks_count = isset($aa_array[$playlist->id]) ? $aa_array[$playlist->id] : 0; ?>
			<div class="col-lg-2 col-md-3 col-sm-4 col-xs-4 scroll-items" style="padding:0; margin-bottom:15px">
				<div class="album">
			        <img src="{{ URL::asset('assets/images/artwork.jpg') }}" alt="{{ $playlist->title }}" class="img-responsive">
			        <div class="play-album" data-playlist="{{ $playlist->slug }}">
	            		<span class="fa fa-play"></span>
	            	</div>
		        	<div class="album-info">
		        		<a title="{{ Lang::get('words.play') }} {{ $playlist->title }}" class="album-title pjax" href="{{ URL::to('playlist/'.$playlist->slug) }}" style="display:block;position:relative;">{{ $playlist->title }}</a>
		        		<span class="album-artist">{{ Lang::get('words.tracks') }} ({{ $tracks_count }})</span>
		        		<div class="clearfix"></div>
		        	</div>
		       	</div>
		    </div>
			@endforeach
			<div class="clearfix"></div>
		</div>
		{{ $playlists->links('pagination::simple') }}
	</div>
</div>
<div class="modal fade" id="create-playlist-modal" tabindex="-1" role="dialog" aria-labelledby="deleteProductModalLabel" aria-hidden="true">
	<div class="modal-dialog">
    	<div class="modal-content">
      		<div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        <h4 class="modal-title">{{ Lang::get('words.create-playlist') }}</h4>
      		</div>
      		<form id="create-playlist-form" role="form" method="post" action="{{ URL::to('playlist') }}">
	      		<div class="modal-body">
			    	<input type="hidden" name="_token" value="{{ csrf_token() }}">
		            <div class="form-group">
						<label>{{ Lang::get('words.playlist-name') }}:</label>
						<input type="text" class="form-control" name="title" placeholder="{{ Lang::get('words.title') }}">
					</div>
	      		</div>
	      		<div class="modal-footer">
	        		<input type="submit" class="btn btn-danger btn-block" value="{{ Lang::get('words.save-playlist') }}">
	      		</div>
	      	</form>
    	</div>
	</div>
</div>
@stop