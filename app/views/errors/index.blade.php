@extends('index')

@section('meta-content')
	<meta name="twitter:card" value="photo"/>
	<meta name="twitter:site" value="{{ '@'.$settings->twitter_page_id }}"/>
	<meta name="twitter:image" value="{{ URL::asset('assets/images/logo.png') }}"/>
	<meta property="og:type" content="blog"/>
	<meta property="og:site_name" content="{{ $settings->website_name }}"/>
	<meta property="og:url" content="{{ URL::to('/') }}"/>
	<meta property="og:title" content="Error: 404, Page Not Found"/>
	<meta property="og:image" content="{{ URL::asset('assets/images/logo.png') }}"/>
	<meta property="og:description" content="Error: 404, Page Not Found"/>
	<meta content="Error: 404, Page Not Found" name="description"/>
	<meta content="Error: 404, Page Not Found" name="title"/>
	<title>Error: 404, Page Not Found</title>
@stop

@section('content')
	<div class="row">
		<div class="col-xs-12 text-center">
			<div class="hidden-xs text-center" style="width:728px;margin:15px auto 0">
				<iframe src="{{ URL::to('ads/banner') }}" width='728' height="90" frameborder='0' border='0' marginwidth='0' marginheight='0' scrolling='no'></iframe>
			</div>
			<h2 class="text-danger"><span class="fa fa-meh-o"></span> Error: 404, Page Not Found</h2>
			<img width="100%" src="{{ URL::asset('assets/images/404.jpg') }}">
		</div>
	</div>
@stop