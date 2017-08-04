@extends('index')
@section('meta-content')
	<meta name="twitter:card" value="photo"/>
	<meta name="twitter:site" value="{{ '@'.$settings->twitter_page_id }}"/>
	<meta name="twitter:image" value="{{ URL::asset('assets/images/logo.png') }}"/>
	<meta property="og:type" content="blog"/>
	<meta property="og:site_name" content="{{ $settings->website_name }}"/>
	<meta property="og:url" content="{{ URL::to('language') }}"/>
	<meta property="og:title" content="{{ $settings->website_name }} - {{ Lang::get('words.all-languages') }}"/>
	<meta property="og:image" content="{{ URL::asset('assets/images/logo.png') }}"/>
	<meta property="og:description" content="{{ $settings->website_name }} - {{ Lang::get('words.all-languages') }}"/>
	<title>{{ $settings->website_name }} - {{ Lang::get('words.all-languages') }}</title>
@stop

@section('content')
<div class="row">
	<div class="col-xs-12">
		<div class="hidden-xs text-center" style="width:728px;margin:15px auto 0">
			<iframe src="{{ URL::to('ads/banner') }}" width='728' height="90" frameborder='0' border='0' marginwidth='0' marginheight='0' scrolling='no'></iframe>
		</div>

		<h2 class="text-muted">Languages</h2>
		<div class="row">
			@foreach($languages as $language)
				<div class="col-lg-2 col-md-3 col-sm-4 col-xs-4 lists">
					<a class="pjax" title="{{ Lang::get('words.browse-albums', array('title' => $language->name)) }}" href="{{ URL::to('language/'.$language->slug) }}">{{ $language->name }}</a>
			    </div>
			@endforeach
		</div>
	</div>
</div>
@stop