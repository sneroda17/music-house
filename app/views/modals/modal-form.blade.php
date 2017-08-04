<div class="modal fade" id="login" tabindex="-1" role="dialog" aria-labelledby="loginLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	@include('modals.login-form')
</div>

<div class="modal fade" id="signup" tabindex="-1" role="dialog" aria-labelledby="signUpLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	@include('modals.signup-form')
</div>

<div class="modal fade" id="emailsignup" tabindex="-1" role="dialog" aria-labelledby="emailSignUpLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	@include('modals.email-signup-form')
</div>

<div class="modal fade" id="forgotpassword" tabindex="-1" role="dialog" aria-labelledby="forgotPasswordUpLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	@include('password.remind')
</div>

<div class="modal fade" id="adminsignup" tabindex="-1" role="dialog" aria-labelledby="forgotPasswordUpLabel" aria-hidden="true" data-keyboard="false">
	@include('modals.admin-signup-form')
</div>