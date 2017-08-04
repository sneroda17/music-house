@extends('index')

@section('meta-content')
	<meta name="twitter:card" value="photo"/>
	<meta name="twitter:site" value="{{ '@'.$settings->twitter_page_id }}"/>
	<meta name="twitter:image" value="{{ URL::asset('assets/images/logo.png') }}"/>
	<meta property="og:type" content="blog"/>
	<meta property="og:site_name" content="{{ $settings->website_name }}"/>
	<meta property="og:url" content="{{ URL::to($page->title) }}"/>
	<meta property="og:title" content="{{ $settings->website_name }} - {{ $page->title }}"/>
	<meta property="og:image" content="{{ URL::asset('assets/images/logo.png') }}"/>
	<meta property="og:description" content="{{ $settings->website_title }}"/>
	<meta content="{{ $page->title }}" name="description"/>
	<meta content="{{ $settings->website_name }} - {{ $page->title }}" name="title"/>
	<title>{{ $settings->website_name }} - {{ $page->title }}</title>
@stop

@section('content')
<div class="hidden-xs text-center" style="width:728px;margin:15px auto 0">
	<iframe src="{{ URL::to('ads/banner') }}" width='728' height="90" frameborder='0' border='0' marginwidth='0' marginheight='0' scrolling='no'></iframe>
</div>
<h1 class="text-muted">{{ $page->title }}</h1>
{{ $page->description }}

<div class="hidden-xs text-center" style="width:728px;margin:15px auto 0">
	<iframe src="{{ URL::to('ads/banner') }}" width='728' height="90" frameborder='0' border='0' marginwidth='0' marginheight='0' scrolling='no'></iframe>
</div>
@stop