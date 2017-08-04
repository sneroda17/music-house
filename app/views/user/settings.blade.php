@extends('index')

	@section('meta-content')
		<meta name="twitter:card" value="photo"/>
		<meta name="twitter:site" value="{{ '@'.$settings->twitter_page_id }}"/>
		<meta name="twitter:image" value="{{ URL::asset('assets/images/logo.png') }}"/>
		<meta property="og:type" content="blog"/>
		<meta property="og:site_name" content="{{ $settings->website_name }}"/>
		<meta property="og:url" content="{{ URL::to('settings') }}"/>
		<meta property="og:title" content="{{ Lang::get('words.account-settings') }} - {{ $settings->website_name }}"/>
		<meta property="og:image" content="{{ URL::asset('assets/images/logo.png') }}"/>
		<meta property="og:description" content="{{ $settings->website_title }}"/>
		<meta content="{{ Lang::get('words.account-settings') }} - {{ $settings->website_name }}" name="description"/>
		<meta content="{{ Lang::get('words.account-settings') }} - {{ $settings->website_name }}" name="title"/>
		<title>{{ Lang::get('words.account-settings') }} - {{ $settings->website_name }}</title>
	@stop

	@section('content')
<div class="row">
	<div class="col-xs-12">
	<div class="hidden-xs text-center" style="width:728px;margin:15px auto 0">
		<iframe src="{{ URL::to('ads/banner') }}" width='728' height="90" frameborder='0' border='0' marginwidth='0' marginheight='0' scrolling='no'></iframe>
	</div>
	<h2 class="text-muted"><span class="fa fa-gear"></span> {{ Lang::get('words.account-settings') }}</h2>
	<br />
	</div>
	<form id="edit-user-form" action="{{ URL::to('settings') }}" method="post">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<input type="hidden" name="user" value="{{ Auth::user()->id }}">
		<div class="form-group">
			<div class="col-sm-3">{{ Lang::get('words.email') }}:</div>
			<div class="col-sm-9">
				<input type="text" class="form-control" name="email" value="@if($user->email != ''){{ $user->email }}@endif" placeholder="name@example.com">
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="form-group">
			<div class="col-sm-3">{{ Lang::get('words.new-password') }}:</div>
			<div class="col-sm-9">
				<input type="password" autocomplete="off" class="form-control" name="password" placeholder="{{ Lang::get('words.password') }}">
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="form-group">
			<div class="col-sm-3">{{ Lang::get('words.confirm-password') }}:</div>
			<div class="col-sm-9">
				<input type="password" class="form-control" name="password_confirmation" placeholder="{{ Lang::get('words.confirm-password') }}">
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="form-group">
			<div class="col-xs-12">
				<button class="btn pull-right btn-labeled btn-primary"><span class="btn-label"><i class="fa fa-save"></i></span> {{ Lang::get('words.save') }}</button>
			</div>
		</div>
	</form>
	<div class="clearfix"></div>
</div>
<br />
@stop