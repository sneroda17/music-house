@extends('index')
@section('meta-content')
	<meta name="twitter:card" value="photo"/>
	<meta name="twitter:site" value="{{ '@'.$settings->twitter_page_id }}"/>
	<meta name="twitter:image" value="{{ URL::asset('assets/images/logo.png') }}"/>
	<meta property="og:type" content="blog"/>
	<meta property="og:site_name" content="{{ $settings->website_name }}"/>
	<meta property="og:url" content="{{ Request::url() }}"/>
	<meta property="og:title" content="{{ $settings->website_name }} - {{ Lang::get('words.my-favorite-albums') }}"/>
	<meta property="og:image" content="{{ URL::asset('assets/images/logo.png') }}"/>
	<meta property="og:description" content="{{ $settings->website_name }} - {{ Lang::get('words.my-favorite-albums') }}"/>
	<title>{{ $settings->website_name }} - {{ Lang::get('words.my-favorite-albums') }}</title>
@stop

@section('content')
<?php
	$al_counts = DB::table('tracks')->whereIn('album_id', $albumLikes->lists('album_id'))->select(DB::raw('album_id, count(id) AS count'))->groupBy('album_id')->get();
	$al_array = array();
	foreach($al_counts as $al_count) {
		$al_array[$al_count->album_id] = $al_count->count;
	}
?>
<div class="row">
	<div class="col-xs-12">
		<div class="hidden-xs text-center" style="width:728px;margin:15px auto 0">
			<iframe src="{{ URL::to('ads/banner') }}" width='728' height="90" frameborder='0' border='0' marginwidth='0' marginheight='0' scrolling='no'></iframe>
		</div>
		<h2><span class="text-muted">{{ Lang::get('words.favorite') }}</span> <a class="fav-link pjax" href="{{ URL::to('favorites') }}" title="{{ Lang::get('words.tracks') }}">{{ Lang::get('words.tracks') }}</a> <a class="fav-link active pjax" href="{{ URL::to('favorites/albums') }}" title="{{ Lang::get('words.albums') }}">{{ Lang::get('words.albums') }}</a></h2>
		<br />
		<div class="scroll-container">
			@forelse($albumLikes as $albumLike)
				<?php
					$album = $albumLike->album;
					$album_likes = isset($al_array[$album->id]) ? $al_array[$album->id] : 0;
				?>
				<div class="col-lg-2 col-md-3 col-sm-4 col-xs-4 scroll-items" style="padding:0; margin-bottom:15px">
			       	<div class="album">
		            	<img src="{{ URL::to('uploads/albums/'.$album->location.'/thumb/'.$album->slug.'.jpg') }}" alt="{{ $album->title }}" class="img-responsive">
		            	<div class="play-album" data-album="{{ $album->slug }}">
		            		<span class="fa fa-play"></span>
		            	</div>
		            	<div class="album-info">
		            		<a class="album-title pjax" title="{{ $album->title }}" href="{{ URL::to('album/'.Custom::slugify($album->title).'-'.$album->slug) }}">{{ $album->title }}</a>
		            		<span class="album-artist">{{ Lang::get('words.tracks') }} ({{ $album_likes }})</span>
		            		<div class="clearfix"></div>
		            	</div>
		           	</div>
			    </div>
			@empty
				<div class="alert alert-danger"><i class="fa fa-meh-o"></i> Sorry, No Albums found</div>
			@endforelse
			<div class="clearfix"></div>
		</div>
		{{ $albumLikes->links('pagination::simple') }}
	</div>
</div>
@stop