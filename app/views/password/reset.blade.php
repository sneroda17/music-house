@extends('index')

	@section('meta-content')
		<meta name="twitter:card" value="photo"/>
		<meta name="twitter:site" value="{{ '@'.$settings->twitter_page_id }}"/>
		<meta name="twitter:image" value="{{ URL::asset('assets/images/logo.png') }}"/>
		<meta property="og:type" content="blog"/>
		<meta property="og:site_name" content="{{ $settings->website_name }}"/>
		<meta property="og:url" content="{{ URL::to('/') }}"/>
		<meta property="og:title" content="{{ $settings->website_name }} - {{ $settings->website_title }}"/>
		<meta property="og:image" content="{{ URL::asset('assets/images/logo.png') }}"/>
		<meta property="og:description" content="{{ Lang::get('words.reset-password') }} - {{ $settings->website_description }}"/>
		<title>{{ Lang::get('words.reset-password') }} - {{ $settings->website_name }}</title>
	@stop

@section('content')

<div class="row">
	<div class="col-xs-12">
	<div class="hidden-xs text-center" style="width:728px;margin:15px auto 0">
			<iframe src="{{ URL::to('ads/banner') }}" width='728' height="90" frameborder='0' border='0' marginwidth='0' marginheight='0' scrolling='no'></iframe>
		</div>

	<h2 class="text-muted">{{ Lang::get('words.reset-password') }}</h2>
	<br />
		<form action="{{ action('RemindersController@postReset') }}" method="post">
					<input type="hidden" name="token" value="{{ $token }}">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<div class="form-group">
						<div class="col-sm-3 padding0">{{ Lang::get('words.new-password') }}:</div>
						<div class="col-sm-9 padding0">
							<input type="password" autocomplete="off" class="form-control" name="password" placeholder="{{ Lang::get('words.password') }}">
						</div>
						<div class="clearfix"></div>
					</div>
					<div class="form-group">
						<div class="col-sm-3 padding0">{{ Lang::get('words.confirm-password') }}:</div>
						<div class="col-sm-9 padding0">
							<input type="password" class="form-control" name="password_confirmation" placeholder="{{ Lang::get('words.confirm-password') }}">
						</div>
						<div class="clearfix"></div>
					</div>
					<div class="form-group">
						<button class="btn pull-right btn-labeled btn-primary"><span class="btn-label"><i class="fa fa-save"></i></span> {{ Lang::get('words.save') }}</button>
					</div>
				</form>
	</div>
	<div class="clearfix"></div>
	<br />
</div>

@stop