<div class="modal-dialog">
<div class="modal-content">
    <form role="form" method="post" action="{{ action('RemindersController@postRemind') }}">
    	<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<div class="modal-header">
			@if(!Request::is('forgotpassword'))<!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>-->@endif
			<h4 class="modal-title" id="signupLabel">{{ Lang::get('words.reset-password') }}</h4>
		</div>
		<div class="modal-body">
			<h4><small>{{ Lang::get('words.reset-password-email') }}</small></h4>
			<div class="form-group form-addon">
				<span class="fa fa-envelope"></span>
				<input type="email" name="email" class="form-control form-addon" id="email" placeholder="{{ Lang::get('words.email-address') }}">
			</div>
    	</div>
		<div class="modal-footer">
			<button type="submit" class="btn btn-info pull-left">{{ Lang::get('words.recover') }}</button>
		</div>
	</form>
</div>
</div>
