@extends('index')
@section('meta-content')
	<meta name="twitter:card" value="photo"/>
	<meta name="twitter:site" value="{{ '@'.$settings->twitter_page_id }}"/>
	<meta name="twitter:image" value="{{ URL::asset('assets/images/logo.png') }}"/>
	<meta property="og:type" content="blog"/>
	<meta property="og:site_name" content="{{ $settings->website_name }}"/>
	<meta property="og:url" content="{{ Request::url() }}"/>
	<meta property="og:title" content="{{ $settings->website_name.' - '.$ptitle }}"/>
	<meta property="og:image" content="{{ URL::asset('assets/images/logo.png') }}"/>
	<meta property="og:description" content="{{ $settings->website_name.' - '.$ptitle }}"/>
	<title>{{ $settings->website_name.' - '.$ptitle }}</title>
@stop

@section('content')
<?php
	$aa_counts = DB::table('tracks')->whereIn('album_id', $albums->lists('id'))->select(DB::raw('album_id, count(id) AS count'))->groupBy('album_id')->get();
	$aa_array = array();
	foreach($aa_counts as $aa_count) {
		$aa_array[$aa_count->album_id] = $aa_count->count;
	}
?>
<div class="row">
	<div class="col-xs-12">
		<div class="hidden-xs text-center" style="width:728px;margin:15px auto 0">
			<iframe src="{{ URL::to('ads/banner') }}" width='728' height="90" frameborder='0' border='0' marginwidth='0' marginheight='0' scrolling='no'></iframe>
		</div>

		<h2>
			<a class="fav-link @if(Request::is('browse')) active @endif pjax" href="{{ URL::to('browse') }}">{{ Lang::get('words.all') }}</a>
			<a class="fav-link @if(Request::is('browse/top')) active @endif pjax" href="{{ URL::to('browse/top') }}">{{ Lang::get('words.top') }}</a>
			<a class="fav-link @if(Request::is('browse/trending')) active @endif pjax" href="{{ URL::to('browse/trending') }}">{{ Lang::get('words.trending') }}</a>
			<a class="fav-link @if(Request::is('browse/popular')) active @endif pjax" href="{{ URL::to('browse/popular') }}">{{ Lang::get('words.popular') }}</a>
		</h2>
		<br />
		<div class="scroll-container">
			@forelse($albums as $index => $album)
				<?php $tracks_count = isset($aa_array[$album->id]) ? $aa_array[$album->id] : 0; ?>
				<div class="col-lg-2 col-md-3 col-sm-4 col-xs-4 scroll-items" style="padding:0; margin-bottom:15px">
			       	<div class="album">
		            	<img src="{{ URL::to('uploads/albums/'.$album->location.'/thumb/'.$album->slug.'.jpg') }}" alt="{{ $album->title }}">
		            	<div class="play-album" data-album="{{ $album->slug }}" data-cover="{{ URL::to('uploads/albums/'.$album->location.'/thumb/'.$album->slug.'.jpg') }}" data-album_title="{{ $album->title }}">
		            		<span class="fa fa-play"></span>
		            	</div>
		            	<div class="album-info">
		            		<a class="album-title pjax" title="{{ $album->title }}" href="{{ URL::to('album/'.Custom::slugify($album->title).'-'.$album->slug) }}">{{ $album->title }}</a>
		            		<span class="album-artist">{{ Lang::get('words.tracks') }} ({{ $tracks_count }})</span>
		            		<div class="clearfix"></div>
		            	</div>
		           	</div>
			    </div>
			@empty
				<div class="alert alert-danger"><i class="fa fa-meh-o"></i> Sorry, No Albums found</div>
			@endforelse
			<div class="clearfix"></div>
		</div>
		<!--{{ $albums->links('pagination::simple') }}-->
	</div>
</div>
@stop