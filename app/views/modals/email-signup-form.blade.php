<div class="modal-dialog">
<div class="modal-content">
    <form role="form" id="signup-form" method="post" action="{{ URL::to('signup') }}">
    	<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<div class="modal-header">
<!--			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>-->
			<h4 class="modal-title" id="signupLabel">{{ Lang::get('words.become-member') }}</h4>
		</div>
		<div class="modal-body">
			<h4><small>{{ Lang::get('words.register-with') }} {{ Lang::get('words.email-address') }}</small></h4>
			<div class="form-group form-addon">
				<span class="fa fa-user"></span>
				<input type="text" class="form-control form-addon" id="username" name="username" placeholder="{{ Lang::get('words.username') }}">
			</div>
			<div class="form-group form-addon">
				<span class="fa fa-envelope"></span>
				<input type="email" name="email" class="form-control form-addon" id="email" placeholder="{{ Lang::get('words.email-address') }}">
			</div>
			<div class="form-group form-addon">
				<span class="fa fa-lock"></span>
				<input type="password" name="password" class="form-control form-addon" id="password" placeholder="{{ Lang::get('words.password') }}">
			</div>
			<div class="form-group form-addon">
				<span class="fa fa-lock"></span>
				<input type="password" name="password_confirmation" class="form-control form-addon" id="password_confirmation" placeholder="{{ Lang::get('words.confirm-password') }}">
			</div>
			<div class="form-group form-addon">
				<div class="input-group">
				{{ Form::captcha() }}
				</div>
			</div>
    	</div>
		<div class="modal-footer">
			<button type="submit" id="btn-signup" class="btn btn-primary pull-left">{{ Lang::get('words.register') }}</button>
		</div>
	</form>
</div>
</div>
