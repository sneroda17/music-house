<div class="modal-dialog">
<div class="modal-content">
    <form role="form" id="login-form" method="post" action="{{ URL::to('login') }}">
    	<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<div class="modal-header">
<!--			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>-->
			<h4 class="modal-title" id="loginLabel">{{ Lang::get('words.login') }}</h4>
		</div>
		<div class="modal-body">
			<h4><small>{{ Lang::get('words.login-with-social') }}</small></h4>
			<div class="social-signup">
				@if(isset($settings->fb_key) && !empty($settings->fb_key))
					<a href="{{ URL::to('auth/facebook') }}" class="btn btn-md bg-facebook"><i class="fa fa-facebook"></i> {{ Lang::get('words.facebook') }}</a>
				@endif
                @if(isset($settings->twitter_key) && !empty($settings->twitter_key))
                	<a href="{{ URL::to('auth/twitter') }}" class="btn btn-md bg-twitter"><i class="fa fa-twitter"></i> {{ Lang::get('words.twitter') }}</a>
                @endif
                @if(isset($settings->google_key) && !empty($settings->google_key))
                	<a href="{{ URL::to('auth/google') }}" class="btn btn-md bg-google"><i class="fa fa-google-plus"></i> {{ Lang::get('words.google') }}</a>
                @endif
			</div>
			<h4><small>{{ Lang::get('words.login-with-email') }}</small></h4>
			<div class="form-group form-addon">
				<span class="fa fa-user"></span>
				<input type="text" class="form-control form-addon" name="email" id="email" placeholder="{{ Lang::get('words.username-or-email') }}">
			</div>
			<div class="form-group form-addon">
				<span class="fa fa-lock"></span>
				<input type="password" name="password" class="form-control form-addon" id="password" placeholder="{{ Lang::get('words.password') }}">
			</div>
			<div class="form-group form-addon">
				<div class="checkbox">
					<label>
						<input type="checkbox" name="remember"> {{ Lang::get('words.remember-me') }}
					</label>
				</div>
			</div>
    	</div>
		<div class="modal-footer">
			<button type="submit" id="btn-login" class="btn btn-primary pull-left">{{ Lang::get('words.login') }}</button>
			<a type="button" data-dismiss="modal" data-toggle="modal" href="{{ URL::to('#forgotpassword') }}" class="btn btn-default btn-link pull-right">{{ Lang::get('words.forgot-password') }}</a>
		</div>
	</form>
</div>
</div>
