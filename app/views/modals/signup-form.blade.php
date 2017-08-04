<div class="modal-dialog" id="signup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-content">
      <div class="modal-header">
<!--        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>-->
        <h4 class="modal-title" id="signUpModal">{{ Lang::get('words.register') }}</h4>
      </div>
		<div class="modal-body">
			<h4><small>{{ Lang::get('words.register-with-social') }}</small></h4>
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
			<h4><small>{{ Lang::get('words.register-with') }} <strong><a class="modal-link" data-dismiss="modal" data-toggle="modal" href="{{ URL::to('#emailsignup') }}">{{ Lang::get('words.email-address') }}</a></strong></small></h4>
    	</div>
    	<div class="modal-footer">
    		<h4 class="text-left"><small>{{ Lang::get('words.already-member') }} <strong><a class="modal-link" data-dismiss="modal" data-toggle="modal" href="{{ URL::to('#login') }}">{{ Lang::get('words.login') }}</a></strong></small></h4>
    	</div>
    </div>
</div>
