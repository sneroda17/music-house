@extends('master')
@section('content')
	<div class="panel">
		<div class="panel-body">			
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 result-container">
				<h1><span class="fg-orange fa fa-music"></span> {{ Lang::get('words.reset_password') }}</h1>
					<div class="sidebar pull-left visible-lg visible-md">
						<div class="ad-box">{{ $ads->box }}</div>
						<div class="affix-sidebar affix-top">
							<div class="ad-box">{{ $ads->box }}</div>
							<div class="ad-box">{{ $ads->box }}</div>
						</div>
					</div>
					<div class="pull-left">
						<div class="row">
							<div class="col-lg-12 col-md-12 col-sm-12">
								<div class="ad-large">{{ $ads->banner_large }}</div>
								<div class="ad-mobile">{{ $ads->mobile }}</div>
							</div>
						</div>
						<form role="form" method="post" action="{{ URL::to('/') }}/reset/{{ $user->activation_code }}">
							<input type="hidden" name="_token" value="{{ csrf_token() }}">
							<div class="form-group">
								<label for="password">{{ Lang::get('words.password') }}</label>
								<div class="input-group">
									<span class="input-group-addon"><span class="fa fa-envelope"></span></span>
									<input type="password" autocomplete="off" name="password" class="form-control" id="password" placeholder="{{ Lang::get('words.password') }}">
								</div>
							</div>
							<div class="form-group">
								<label for="password_confirmation">{{ Lang::get('words.confirm_password') }}</label>
								<div class="input-group">
									<span class="input-group-addon"><span class="fa fa-envelope"></span></span>
									<input type="password" name="password_confirmation" class="form-control" id="password_confirmation" placeholder="{{ Lang::get('words.confirm_password') }}">
								</div>
							</div>
							<input type="hidden" id="email" name="email" value="{{ $user->email }}" />
							<input type="submit" class="btn btn-info" value="{{ Lang::get('words.update_profile') }}" />
						</form>
					</div>
					<div class="pull-right hidden-800" style="width:160px; height:600px">
						<div class="right-ad">{{ $ads->skyscrapper }}</div>
					</div>
				</div>
			</div>
		</div>
	</div>

@stop
