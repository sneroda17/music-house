@extends('admin.index')

@section('meta-content')
<title>{{ Lang::get('words.admin_alt').' - '.Lang::get('words.users') }}</title>
@stop

@section('content')
<?php
    $pl_counts = DB::table('playlists')->whereIn('user_id', $users->lists('id'))->select(DB::raw('user_id, count(id) as count'))->groupBy('user_id')->get();
    $pl_array = array();
    foreach($pl_counts as $pl_count) {
        $pl_array[$pl_count->user_id] = $pl_count->count;
    }

    $al_counts = DB::table('album_likes')->whereIn('user_id', $users->lists('id'))->select(DB::raw('user_id, count(id) as count'))->groupBy('user_id')->get();
    $al_array = array();
    foreach($al_counts as $al_count) {
        $al_array[$al_count->user_id] = $al_count->count;
    }

    $tl_counts = DB::table('track_likes')->whereIn('user_id', $users->lists('id'))->select(DB::raw('user_id, count(id) as count'))->groupBy('user_id')->get();
    $tl_array = array();
    foreach($tl_counts as $tl_count) {
        $tl_array[$tl_count->user_id] = $tl_count->count;
    }
?>
<div class="row">
    <div class="col-xs-12">
        <ol class="breadcrumb">
            <li><a href="{{ URL::to('admin') }}"><i class="fa fa-dashboard"></i> {{ Lang::get('words.admin') }}</a></li>
            <li class="active">{{ Lang::get('words.users') }} (<b>{{ DB::table("users")->count() }}</b>)</li>
        </ol>
    </div>
</div>
<div class="row">
    <form method="get">
        <div class="col-xs-12">
            <div class="form-group input-group">
                <input type="text" class="form-control" name="q" value="@if(Input::get('q')){{ Input::get('q') }}@endif" placeholder="{{ Lang::get('words.search') }}" aria-describedby="basic-addon2">
                <div class="input-group-btn">
                    <button type="submit" class="btn btn-primary"><span class="fa fa-search"></span> {{ Lang::get('words.search') }}</button>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
    </form>

    <div class="col-md-2 col-sm-2 col-xs-12">
        <div class="accout-info">
            <!--login satrt-->

            <!-- Large modal -->
            <button class="btn btn-primary"  data-toggle="modal" data-target="#adminsignup" href="#adminsignup"> {{ Lang::get('words.add-new-user') }}</button>
            <!--login end-->

        </div>   <!--accout-info-->
    </div>


</div><br>




<div class="row">
    <div class="col-xs-12">
        <div class="table-responsive">
            <table class="table">

            
                <tr>
                    <th>{{ Lang::get('words.username') }}</th>
                    <th>{{ Lang::get('words.email') }}</th>
                    <th>{{ Lang::get('words.playlists') }}</th>
                    <th>{{ Lang::get('words.liked-albums-tracks') }}</th>
                    <th>{{ Lang::get('words.created') }}</th>
                    <th>{{ Lang::get('words.active') }}</th>
                    <th>{{ Lang::get('words.actions') }}</th>
                    <th>{{ Lang::get('words.subscriber') }}</th>


                </tr>
            
                @foreach($users as $user)
                    <?php
                        $playlists_count = isset($pl_array[$user->id]) ? $pl_array[$user->id] : 0;
                        $albums_count = isset($al_array[$user->id]) ? $al_array[$user->id] : 0;
                        $tracks_count = isset($tl_array[$user->id]) ? $tl_array[$user->id] : 0;
                    ?>
                    <tr>
                        <td>{{ $user->username }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $playlists_count }}</td>
                        <td><label class="label label-primary">{{ Lang::get('words.albums') }}: {{ $albums_count }}</label> <label class="label label-primary">{{ Lang::get('words.tracks') }}: {{ $tracks_count }}</label></td>
                        <td>{{ $user->created_at->toFormattedDateString() }}</td>
                        <td>
                            @if($user->active)
                                <label class="label label-success">{{ Lang::get('words.yes') }}</label>
                            @else
                                <label class="label label-warning">{{ Lang::get('words.no') }}</label>
                            @endif
                        </td>

                        <td>
                            @if($user->admin)
                                <a data-target="#user-mode-modal" data-toggle="modal" data-user="{{ $user->id }}" title="{{ Lang::get('words.make') }} {{ Lang::get('words.user') }}" class="btn btn-xs btn-success"><span class="fa fa-user-secret"></span> {{ Lang::get('words.admin') }}</a>
                            @else
                                <a data-target="#user-mode-modal" data-toggle="modal" data-user="{{ $user->id }}" title="{{ Lang::get('words.make') }} {{ Lang::get('words.admin') }}" class="btn btn-xs btn-warning"><span class="fa fa-user"></span> {{ Lang::get('words.user') }}</a>
                            @endif

                            <a data-target="#edit-user-modal" data-toggle="modal" data-user="{{ $user->id }}" data-username="{{ $user->username }}" data-email="{{ $user->email }}" title="{{ Lang::get('words.edit-user') }}" class="btn btn-xs btn-danger"><span class="fa fa-edit"></span> {{ Lang::get('words.edit') }}</a>

                            @if($user->banned)
                                <a data-target="#user-status-modal" data-toggle="modal" data-user="{{ $user->id }}" title="Unban User" class="btn btn-xs btn-danger"><span class="fa fa-ban"></span> {{ Lang::get('words.unban') }}</a>
                            @else
                                <a data-target="#user-status-modal" data-toggle="modal" data-user="{{ $user->id }}" title="Ban User" class="btn btn-xs btn-success"><span class="fa fa-check"></span> {{ Lang::get('words.ban') }}</a>
                            @endif
                        </td>

                        <td>
                            @if(isset($user->subscriber->is_subscribed))
                            <?php
                                   $time_limit = $user->subscriber->time_limit;

                                   $day     = date("j",strtotime($time_limit));
                                   $month   = date("m",strtotime($time_limit));
                                   $year    = date("Y",strtotime($time_limit));
                            ?>
                                <a data-target="#subscribe-modal" data-toggle="modal" data-user="{{ $user->id }}" data-subscription="{{ $user->subscriber->is_subscribed }}" data-expire_date="{{ $day }}" data-expire_month="{{ $month }}" data-expire_year="{{ $year }}" data-download_limit="{{ $user->subscriber->download_limit }}" title="{{ Lang::get('words.user') }} {{ Lang::get('words.subscribed') }}" class="btn btn-xs btn-success">{{ Lang::get('words.yes') }}</a>
                            @else

                                <a data-target="#subscribe-modal" data-toggle="modal" data-user="{{ $user->id }}" title="{{ Lang::get('words.no') }} {{ Lang::get('words.subscribed') }}"  class="btn btn-xs btn-warning">{{ Lang::get('words.no') }}</a>

                            @endif
                        </td>
                    </tr>
                    <?php //dd($user->subscriber->time_limit);
                     //dd($users->subscriber); ?>
                @endforeach
            </table>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12 text-center">
        {{ $users->links() }}
    </div>
</div>

<div class="modal fade" id="edit-user-modal" tabindex="-1" role="dialog" aria-labelledby="deleteProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{{ Lang::get('words.edit-user') }}</h4>
            </div>
            <form id="edit-user-form" role="form" method="post" action="{{ URL::to('admin/user/edit') }}">
                <div class="modal-body">
                    <input type="hidden" name="user" value="">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="form-group">

                        <label>{{ Lang::get('words.username') }}:</label>
                        <input type="text" class="form-control" name="username" placeholder="{{ Lang::get('words.username') }}" disabled>

                    </div>
                    <div class="form-group">

                        <label>{{ Lang::get('words.email') }}:</label>
                        <input type="text" class="form-control" name="email" placeholder="{{ Lang::get('words.email') }}" disabled>

                    </div>
                    <div class="form-group">
                        <label>{{ Lang::get('words.new-password') }}:</label>
                        <input type="password" name="password" autocomplete="off" class="form-control" placeholder="{{ Lang::get('words.new-password') }}" required>
                    </div>
                    <div class="form-group">
                        <label>{{ Lang::get('words.confirm-password') }}:</label>
                        <input type="password" name="password_confirmation" autocomplete="off" class="form-control" placeholder="{{ Lang::get('words.confirm-password') }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="submit" class="btn btn-danger btn-block" value="{{ Lang::get('words.save-user') }}">
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="user-mode-modal" tabindex="-1" role="dialog" aria-labelledby="deleteProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{{ Lang::get('words.change-user-mode') }}</h4>
            </div>
            <form id="user-mode-form" role="form" method="post" action="{{ URL::to('admin/user/mode') }}">
                <div class="modal-body">
                    <input type="hidden" name="user" value="">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <h5>{{ Lang::get('words.change-user-mode-msg') }}</h5>
                </div>
                <div class="modal-footer">
                    <input type="submit" class="btn btn-danger btn-block" value="{{ Lang::get('words.change-mode') }}">
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="user-status-modal" tabindex="-1" role="dialog" aria-labelledby="deleteProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{{ Lang::get('words.change-user-status') }}</h4>
            </div>
            <form id="user-status-form" role="form" method="post" action="{{ URL::to('admin/user/status') }}">
                <div class="modal-body">
                    <input type="hidden" name="user" value="">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <h5>{{ Lang::get('words.change-user-status-msg') }}</h5>
                </div>
                <div class="modal-footer">
                    <input type="submit" class="btn btn-danger btn-block" value="{{ Lang::get('words.change-status') }}">
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="subscribe-modal" tabindex="-1" role="dialog" aria-labelledby="deleteProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{{ Lang::get('words.user-subs') }}</h4>
            </div>

            <form id="subscribe-form" role="form" method="POST" action="{{ URL::to('admin/user/subscriber') }}">
                <div class="modal-body">
                    <input type="hidden" name="user" value="">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <small class="check">{{ Lang::get('words.premium-membership') }}</small>
                    @if(Auth::user()->admin)
                        <input type="checkbox" class="check" name="subscription" value="1" >
                    @else
                        <input type="checkbox" class="check" name="subscription" value=1>
                    @endif
                    <h4><small>{{ Lang::get('words.subs-expiry-date') }}</small></h4>
                    @if(Auth::user()->admin)
                    <div class="subs-date">

                        <select id="expire_date" name="expire_date" class="custom-select" >
                        <option value=""> Date </option>
                            @for ($i = 1; $i <= 31; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor

                        </select>
                       <select id="expire_month" name="expire_month" >
                              <option value=''> Month </option>
                              <option value="01">Janaury</option>
                              <option value="02">February</option>
                              <option value="03">March</option>
                              <option value="04">April</option>
                              <option value="05">May</option>
                              <option value="06">June</option>
                              <option value="07">July</option>
                              <option value="08">August</option>
                              <option value="09">September</option>
                              <option value="10">October</option>
                              <option value="11">November</option>
                              <option value="12">December</option>
                        </select>
                        <select id="expire_year" name="expire_year" >
                              <option value=""> Year </option>
                              <option value="2017">2017</option>
                              <option value="2018">2018</option>
                              <option value="2019">2019</option>
                              <option value="2020">2020</option>

                        </select>
                        </div>
                    @else
                        <div class="subs-date">

                            <select id='expire_date' name="expire_date"  >
                            <option value=''> Date </option>
                                @for ($i = 1; $i <= 31; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                           <select id='expire_month' name="expire_month" disabled>
                                  <option value=''> Month </option>
                                  <option value='01'>Janaury</option>
                                  <option value='02'>February</option>
                                  <option value='03'>March</option>
                                  <option value='04'>April</option>
                                  <option value='05'>May</option>
                                  <option value='06'>June</option>
                                  <option value='07'>July</option>
                                  <option value='08'>August</option>
                                  <option value='09'>September</option>
                                  <option value='10'>October</option>
                                  <option value='11'>November</option>
                                  <option value='12'>December</option>
                            </select>
                            <select id='expire_year' name="expire_year" >
                                  <option value=''> Year </option>
                                  <option value='2017'>2017</option>
                                  <option value='2018'>2018</option>
                                  <option value='2019'>2019</option>
                                  <option value='2020'>2020</option>

                            </select>
                            </div>
                    @endif
                    <h4><small>{{ Lang::get('words.download-limit-left') }}</small></h4>
                    @if(Auth::user()->admin)
                       <div class="down">
                       <Span>
                        <input type="text" class="form-control form-addon" name="download_limit" >
                        </Span>
                       <Span class="quota"> MB</Span>
                        </div>
                    @else
                    <div class="down">
                       <Span>
                        <input type="text" class="form-control form-addon" name="download_limit" >
                        </Span>
                       <Span class="quota"> MB</Span>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <input type="submit" id="btn-subscribe" class="btn btn-danger btn-block" value="{{ Lang::get('words.submit') }}">
                </div>
            </form>

        </div>
    </div>
</div>
@stop



@include('modals.modal-form')
<script type="text/javascript">
    @if(Session::get('message') != '' && Session::get('status') != '')
        var n = noty({
                text        : '{{ Session::get("message") }}',
                type        : '{{ Session::get("status") }}',
                dismissQueue: true,
                layout      : 'bottomRight',
                closeWith   : ['click'],
                theme       : 'relax',
                maxVisible  : 10
            });
        <?php Session::forget('status'); Session::forget('message'); ?>
    @endif

</script>
@if(isset($settings->analytics))
    {{ htmlspecialchars_decode($settings->analytics) }}
@endif
{{--
</body>
</html>--}}
