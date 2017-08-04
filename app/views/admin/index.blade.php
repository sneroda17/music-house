<!doctype html>
<html lang="en">
<head>
	@yield('meta-content')
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
	<meta name="rootURL" content="{{ URL::to('/') }}">
	<meta name="_token" content="{{ csrf_token() }}">
	<meta name="youtube_key" content="{{ $settings->youtube_key }}">
	
	<link rel="stylesheet" href="{{ URL::asset('assets/css/bootstrap.css') }}" />
	<link rel="stylesheet" href="{{ URL::asset('assets/css/font-awesome.css') }}" />
	<link rel="stylesheet" href="{{ URL::asset('assets/css/jquery.bxslider.css') }}" />
	<link rel="stylesheet" type="text/css" href="{{ URL::to('assets/css/bootstrap-datepicker3.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ URL::to('assets/css/bootstrap-colorpicker.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('assets/css/style.css?v=2') }}" />

    <link rel="icon" href="{{ URL::asset('assets/images/favicon.ico') }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ URL::asset('assets/images/favicon.ico') }}" type="image/x-icon">

    <script type="text/javascript" src="{{ URL::asset('assets/js/jquery-1.11.0.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('assets/js/bootstrap.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('assets/js/jscroll.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('assets/js/jquery.form.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('assets/js/jquery.pjax.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('assets/js/bootstrap-tagsinput.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('assets/js/typeahead.bundle.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('assets/js/nprogress.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('assets/js/jquery.bxslider.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('assets/js/jquery.noty.js') }}"></script>
	<script type="text/javascript" src="{{ URL::to('assets/js/bootstrap-datepicker.js') }}"></script>
	<script type="text/javascript" src="{{ URL::asset('assets/js/bootstrap-colorpicker.js') }}"></script>
    @yield('resources')
    <script type="text/javascript" src="{{ URL::asset('assets/js/app.js?v=2') }}"></script>
    <script type="text/javascript">
      $(document).ready(function(){
        rootURL = "{{ URL::to('/') }}";
      });
    </script>
</head>
<body>
<div id="wrapper" class="hidden">
	<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
		<div class="navbar-header">
			<a class="navbar-brand pjax" title="{{ Lang::get('words.admin_alt') }}" href="{{ URL::to('admin') }}">{{ Lang::get('words.admin_alt') }}</a>
		</div>
	</nav>
	<nav class="navbar-inverse">
		<ul class="nav navbar-nav side-nav hidden-xs">
	        <li @if(Request::is('admin/album') || Request::is('admin'))  @endif>
           	            <a class="pjax" href="{{ URL::to('admin/bulkupload') }}"><i class="fa fa-2x fa-dot-circle-o"></i> {{ Lang::get('words.bulk-upload') }}</a>
            </li>
	        <li @if(Request::is('admin/album') || Request::is('admin')) class="active" @endif>
	            <a class="pjax" href="{{ URL::to('admin/album') }}"><i class="fa fa-2x fa-dot-circle-o"></i> {{ Lang::get('words.albums') }}</a>
	        </li>
	        <li @if(Request::is('admin/artist')) class="active" @endif>
	            <a class="pjax" href="{{ URL::to('admin/artist') }}"><i class="fa fa-2x fa-microphone"></i> {{ Lang::get('words.artists') }}</a>
	        </li>
	        <li @if(Request::is('admin/user')) class="active" @endif>
	            <a class="pjax" href="{{ URL::to('admin/user') }}"><i class="fa fa-2x fa-user"></i> {{ Lang::get('words.users') }}</a>
	        </li>
	        <li @if(Request::is('admin/categories')) class="active" @endif>
	            <a class="pjax" href="{{ URL::to('admin/categories') }}"><i class="fa fa-2x fa-list"></i> {{ Lang::get('words.categories') }}</a>
	        </li>
	        <li @if(Request::is('admin/languages')) class="active" @endif>
	            <a class="pjax" href="{{ URL::to('admin/languages') }}"><i class="fa fa-2x fa-globe"></i> {{ Lang::get('words.languages') }}</a>
	        </li>
	        <li @if(Request::is('admin/settings')) class="active" @endif>
	            <a class="pjax" href="{{ URL::to('admin/settings') }}"><i class="fa fa-2x fa-gear"></i> {{ Lang::get('words.settings') }}</a>
	        </li>
	        <li @if(Request::is('admin/pages')) class="active" @endif>
	            <a class="pjax" href="{{ URL::to('admin/pages') }}"><i class="fa fa-2x fa-file-text"></i> {{ Lang::get('words.pages') }}</a>
	        </li>
	    </ul>
	</nav>
	<div id="page-wrapper">
		<div class="container-fluid">
			<div class="col-xs-12">
			<style type="text/css">
    #wrapper {
    	padding: 0 0 0 70px;
    }
    #page-wrapper {
    	padding-top: 15px;
    }
    ul.pagination {
    	opacity: 1;
    }
    </style>
				@yield('content')
			</div>
		</div>
	</div>
</div>
</body>
</html>