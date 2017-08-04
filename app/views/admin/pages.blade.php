@extends('admin.index')
@section('meta-content')
<title>{{ Lang::get('words.admin_alt').' - '.Lang::get('words.pages') }}</title>
@stop
@section('content')
<div class="row">
    <div class="col-xs-12">
        <ol class="breadcrumb">
            <li><a href="{{ URL::to('admin') }}">{{ Lang::get('words.admin') }}</a></li>
            <li class="active">{{ Lang::get('words.pages') }}</li>
        </ol>
    </div>
</div>
<form method="POST" action="{{ URL::to('admin/pages') }}" accept-charset="UTF-8">
    <div class="row">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
		@foreach($pages as $page)
            <div class="col-lg-6">
                <div class="form-group">
                    <label for="{{ $page->title }}" class="control-label padding0">{{ $page->title }}:</label>
                    <textarea rows="6" class="form-control" name="{{ $page->title }}">{{ $page->description }}</textarea>
                </div>
            </div>
        @endforeach
    </div>
    <div class="row">
        <div class="col-xs-12">
            <button type="submit" class="btn btn-primary btn-block">{{ Lang::get('words.save') }} {{ Lang::get('words.pages') }}</button>
        </div>
    </div>
</form>
@stop