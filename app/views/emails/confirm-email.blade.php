@extends('index')

@section('meta-content')
	<meta name="twitter:card" value="photo"/>
	<meta name="twitter:site" value="{{ '@'.$settings->twitter_page_id }}"/>
	<meta name="twitter:image" value="{{ URL::asset('assets/images/logo.png') }}"/>
	<meta property="og:type" content="blog"/>
	<meta property="og:site_name" content="{{ $settings->website_name }}"/>
	<meta property="og:url" content="{{ URL::to('piemaker') }}"/>
	<meta property="og:title" content="{{ $settings->website_name }} - {{ Lang::get('words.register-success') }}"/>
	<meta property="og:image" content="{{ URL::asset('assets/images/logo.png') }}"/>
	<meta property="og:description" content="{{ $settings->website_title }}"/>
	<meta content="{{ Lang::get('words.register-success') }}" name="description"/>
	<meta content="{{ $settings->website_name }} - {{ Lang::get('words.register-success') }}" name="title"/>
	<title>{{ Lang::get('words.register-success') }} - {{ $settings->website_name }}</title>
@stop

@section('content')
	<div class="lt-container pull-left">
		<div class="alert alert-danger">
		    <i class="fa fa-info-circle"></i> Your account is not activated. Please check your e-mail and click the activation link. If you didn't get the activation e-mail <a href="" class="alert-link"><u>click here</u></a> to resend it. By activating your account you will be able to add content and comment on posts. You can also change your e-mail address on your account settings
		</div>
		<form method="post" action="{{ URL::to('resend') }}">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<div class="form-group input-group">
				<input type="text" placeholder="user@example.com" value="{{ Auth::user()->email }}" class="form-control">
				<span class="input-group-btn"><button type="button" class="btn btn-primary"><i class="fa fa-check"></i></button></span>
			</div>			
		</form>
	</div>
	<div class="pull-right hidden-xs rt-sidebar">
		<div class="fixed">
			<div class="ad-box">
				{{ htmlspecialchars_decode($settings->ad_code) }}
			</div>
			<div class="social-box">
				<iframe frameborder="0" allowtransparency="true" style="border:none;overflow:hidden;width:285px;height:65px;" scrolling="no" src="//www.facebook.com/plugins/like.php?href=http%3A%2F%2Ffacebook.com%2F{{$settings->fb_page_id}}&amp;width=259&amp;layout=standard&amp;action=like&amp;show_faces=true&amp;share=true&amp;height=80&amp;appId=396278673807902"></iframe>
				<hr>
				<iframe id="twitter-widget-0" class="twitter-follow-button twitter-follow-button" frameborder="0" scrolling="no" allowtransparency="true" src="http://platform.twitter.com/widgets/follow_button.1401325387.html#_=1401719332352&id=twitter-widget-0&lang=en&screen_name={{$settings->twitter_page_id}}&show_count=true&show_screen_name=true&size=l" title="Twitter Follow Button" data-twttr-rendered="true" style="width: 297px; height: 28px"></iframe>
				<hr>
				<div class="pull-left">
					<div class="g-follow" data-annotation="bubble" data-height="24" data-href="//plus.google.com/u/0/{{$settings->google_page_id}}" data-rel="publisher"></div>
				</div>
				<div class="pull-right">
					<div class="g-plusone"></div>
				</div>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>
	<div class="clearfix"></div>
@stop
