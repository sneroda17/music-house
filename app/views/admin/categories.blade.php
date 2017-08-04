@extends('admin.index')
@section('meta-content')
<title>{{ Lang::get('words.admin_alt').' - '.Lang::get('words.categories') }}</title>
@stop
@section('content')
<div class="row">
    <div class="col-xs-12">
        <ol class="breadcrumb">
            <li><a href="{{ URL::to('admin') }}">{{ Lang::get('words.admin') }}</a></li>
            <li class="active">{{ Lang::get('words.categories') }} (<b>{{ DB::table("categories")->count() }}</b>)</li>
        </ol>
    </div>
</div>
<div class="row">
	@foreach($categories as $category)
        <form class="cat-form" method="POST" action="{{ URL::to('admin/category/'.$category->id) }}" accept-charset="UTF-8">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="col-md-3 col-sm-4">
                <div class="form-group input-group">
                    <input type="text" class="form-control" name="name" value="{{ $category->name }}">
                    <div class="input-group-btn">
                        <button  class="btn btn-primary">{{ Lang::get('words.save') }}</button>
                    </div>
                </div>
            </div>
        </form>
    @endforeach
</div>
<div class="row">
    <div class="col-xs-12 text-center">
        {{ $categories->links() }}
    </div>
</div>
@stop