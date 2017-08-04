@extends('admin.index')
@section('meta-content')
<title>{{ Lang::get('words.admin_alt').' - '.Lang::get('words.settings') }}</title>
@stop
@section('content')
<div class="row">
	<div class="col-xs-12">
		<ol class="breadcrumb">
			<li><a href="{{ URL::to('admin') }}">{{ Lang::get('words.admin') }}</a></li>
			<li class="active">{{ Lang::get('words.settings') }}</li>
		</ol>
	</div>
</div>
	<form method="POST" action="{{ URL::to('admin/settings') }}" id="media-form" accept-charset="UTF-8" file="1" enctype="multipart/form-data">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<div class="row">
			<div class="col-lg-4">
				<div class="form-group">
					<label for="website_name" class="control-label">{{ Lang::get('words.website-name') }}:</label>
					<input type="text" class="form-control" name="website_name" value="{{ $settings->website_name }}">
				</div>
				<div class="form-group">
					<label for="website_title" class="control-label">{{ Lang::get('words.website-title') }}:</label>
					<input type="text" class="form-control" name="website_title" value="{{ $settings->website_title }}">
				</div>
				<div class="form-group">
					<label for="website_description" class="control-label">{{ Lang::get('words.website-description') }}:</label>
					<input type="text" class="form-control" name="website_description" value="{{ $settings->website_description }}">
				</div>
				<div class="form-group">
					<label for="logo_image" class="control-label">{{ Lang::get('words.website-logo') }}:</label>
					<img class="img-responsive" src="{{ URL::asset('assets/images/logo.png') }}">
					<span class="btn btn-danger btn-file btn-block">
						{{ Lang::get('words.select-image') }}
						<input class="btn" type="file" name="logo_image">
					</span>
				</div>
				<div class="form-group">
					<label for="website_description" class="control-label">{{ Lang::get('words.recaptcha-key') }}:</label>
					<input type="text" class="form-control" name="recaptcha_site_key" value="{{ $settings->recaptcha_site_key }}">
				</div>
				<div class="form-group">
					<label for="website_description" class="control-label">{{ Lang::get('words.recaptcha-secret-key') }}:</label>
					<input type="text" class="form-control" name="recaptcha_secret_key" value="{{ $settings->recaptcha_secret_key }}">
				</div>
				<div class="form-group">
					<label for="analytics" class="control-label">{{ Lang::get('words.google-analytics') }}:</label>
					<textarea class="form-control" rows="5" name="analytics">{{ $settings->analytics }}</textarea>
				</div>
			</div>
			<div class="col-lg-4">
				<div class="form-group">
					<label for="website_name" class="control-label">{{ Lang::get('words.facebook-page') }}:</label>
					<input type="text" class="form-control" name="fb_page_id" value="{{ $settings->fb_page_id }}">
				</div>
				<div class="form-group">
					<label for="website_name" class="control-label">{{ Lang::get('words.facebook-key') }}:</label>
					<input type="text" class="form-control" name="fb_key" value="{{ $settings->fb_key }}">
				</div>
				<div class="form-group">
					<label for="website_name" class="control-label">{{ Lang::get('words.facebook-secret-key') }}:</label>
					<input type="text" class="form-control" name="fb_secret_key" value="{{ $settings->fb_secret_key }}">
				</div>
				<div class="form-group">
					<label for="website_name" class="control-label">{{ Lang::get('words.twitter-page') }}:</label>
					<input type="text" class="form-control" name="twitter_page_id" value="{{ $settings->twitter_page_id }}">
				</div>
				<div class="form-group">
					<label for="website_name" class="control-label">{{ Lang::get('words.twitter-key') }}:</label>
					<input type="text" class="form-control" name="twitter_key" value="{{ $settings->twitter_key }}">
				</div>
				<div class="form-group">
					<label for="website_name" class="control-label">{{ Lang::get('words.twitter-secret-key') }}:</label>
					<input type="text" class="form-control" name="twitter_secret_key" value="{{ $settings->twitter_secret_key }}">
				</div>
				<div class="form-group">
					<label for="website_name" class="control-label">{{ Lang::get('words.google-page') }}:</label>
					<input type="text" class="form-control" name="google_page_id" value="{{ $settings->google_page_id }}">
				</div>
				<div class="form-group">
					<label for="website_name" class="control-label">{{ Lang::get('words.google-key') }}:</label>
					<input type="text" class="form-control" name="google_key" value="{{ $settings->google_key }}">
				</div>
				<div class="form-group">
					<label for="website_name" class="control-label">{{ Lang::get('words.google-secret-key') }}:</label>
					<input type="text" class="form-control" name="google_secret_key" value="{{ $settings->google_secret_key }}">
				</div>
			</div>
			<div class="col-lg-4">
				<div class="form-group">
					<label for="youtube_key" class="control-label">{{ Lang::get('words.youtube-key') }}:</label>
					<input type="text" class="form-control" name="youtube_key" value="{{ $settings->youtube_key }}">
				</div>
				<div class="form-group">
					<label class="control-label">Accent Color:</label>
					{{ Form::text('theme_color', $settings->theme_color, array('class'=>'form-control theme-color')) }}
				</div>
				<div class="form-group">
					<div class="funkyradio">
	            		<div class="funkyradio-success">
				            <input type="checkbox" name="auth_download" @if($settings->auth_download) checked @endif id="radio1" />
				            <label for="radio1">{{ Lang::get('words.auth-download-msg') }}</label>
				        </div>
		            </div>
				</div>
				<div class="form-group">
					<div class="funkyradio">
	            		<div class="funkyradio-success">
				            <input type="checkbox" name="downloadable" @if($settings->downloadable) checked @endif id="radio2" />
				            <label for="radio2">{{ Lang::get('words.downloadable-msg') }}</label>
				        </div>
		            </div>
				</div>
				<div class="form-group">
					<div class="funkyradio">
	            		<div class="funkyradio-success">
				            <input type="checkbox" name="zip_download" @if($settings->zip_download) checked @endif id="radio3" />
				            <label for="radio3">{{ Lang::get('words.zip-download-msg') }}</label>
				        </div>
		            </div>
				</div>
				<div class="form-group">
					<label for="box_ad" class="control-label">{{ Lang::get('words.box-ad-msg') }}:</label>
					<textarea class="form-control" rows="5" name="box_ad">{{ $settings->box_ad }}</textarea>
				</div>
				<div class="form-group">
					<label for="banner_ad" class="control-label">{{ Lang::get('words.banner-ad-msg') }}:</label>
					<textarea class="form-control" rows="5" name="banner_ad">{{ $settings->banner_ad }}</textarea>
				</div>
			</div>
			<div class="clearfix"></div>
		</div>
		<br>
		<button type="submit" class="btn btn-lg btn-block btn-primary">{{ Lang::get('words.update-settings') }}</button>
	</form>
<br>
<br>
<script type="text/javascript">
	$(document).ready(function(){
			$(document).on('focus', '.theme-color', function(){
					$(this).colorpicker();
			});
	        
		});
</script>
@stop