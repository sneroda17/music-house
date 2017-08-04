@extends('admin.index')
@section('meta-content')
<title>{{ Lang::get('words.admin_alt').' - '.Lang::get('words.pages') }}</title>
@stop
@section('content')
<div class="row">
    <div class="col-xs-12">
        <ol class="breadcrumb">
            <li><a href="{{ URL::to('admin') }}">{{ Lang::get('words.admin') }}</a></li>
            <li class="active">{{ Lang::get('words.bulk-upload') }} </li>
        </ol>
    </div>
</div>
<div class="row">

        <form class="bulkupload-form" id="bulkupload-form"  method="POST" action="{{ URL::to('admin/startbulkupload') }}" accept-charset="UTF-8">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="col-md-3 col-sm-4">
        /public/
            <div class="form-group input-group">
                <input type="text" class="form-control" name="path" value="bulkuploadfiles">
                <div class="input-group-btn">
                    <button  id="startbulkupload" class="btn btn-primary startbulkupload" >{{ Lang::get('words.start-upload') }}</button>
                </div>
            </div>
        </div>
    </form>

</div>

@stop