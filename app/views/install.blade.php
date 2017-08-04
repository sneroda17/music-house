<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="{{ URL::asset('assets/css/bootstrap.css') }}" />
    <link rel="stylesheet" href="{{ URL::asset('assets/css/font-awesome.css') }}" />
    <link rel="stylesheet" href="{{ URL::asset('assets/css/jquery.toastmessage.css') }}" />
    <link rel="stylesheet" href="{{ URL::asset('assets/css/style.css') }}" />

   <style>
   body{
    background-color: #EAEAEA;
   }
   </style>

</head>
<body>
 <div style="text-align:center;margin-top:20px; color:#333"><h4>Install Database Module MP3 Gallery Script</h4></div>
<div class="container" style="background-color:#FFFFFF;margin-top:20px;border-radius:5px">
    <div class="row" style="padding:15px">    
    <form  action="{{ URL::to('install') }}" method="post" role="form" style="margin-top:30px;">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
        @if(Session::get('message') != '')
            <div class="alert alert-danger" style="font-size:14px;">                                                   
                <i class="fa fa-database"></i> {{ Session::get('message') }}
            </div>
            <?php Session::forget('message'); ?>
        @endif
        <div class="col-sm-6">
          <legend><i class="fa fa-gears"></i> Setup Requirements</legend>
          <div class="row" style="color:#757575">
                <div class="col-xs-12">
                    PHP Version 5.4 +
                    <span class="label label-<?php if (phpversion() >= 5.4) { echo "success"; } else { echo "danger"; } ?> pull-right">Required</span>
                </div>
                <div class="col-xs-12">
                    CURL
                    <span class="label label-<?php if (extension_loaded('curl')) { echo "success"; } else { echo "danger"; } ?> pull-right">Required</span>
                </div>
                <div class="col-xs-12">
                    PDO Extension
                    <span class="label label-<?php if (extension_loaded('pdo')) { echo "success"; } else { echo "danger"; } ?> pull-right">Required</span>
                </div>
                <div class="col-xs-12">
                    GD Extension
                    <span class="label label-<?php if (extension_loaded('gd')) { echo "success"; } else { echo "danger"; } ?> pull-right">Required</span>
                </div>
                <div class="col-xs-12">
                    FileInfo Extension
                    <span class="label label-<?php if (extension_loaded('fileinfo')) { echo "success"; } else { echo "danger"; } ?> pull-right">Required</span>
                </div>
                <div class="col-xs-12">
                    MYSQLi Extension
                    <span class="label label-<?php if (extension_loaded('mysqli')) { echo "success"; } else { echo "danger"; } ?> pull-right">Required</span>
                </div> 
                <div class="col-xs-12">
                   Allow Url Fopen
                    <span class="label label-<?php if (ini_get('allow_url_fopen')) { echo "success"; } else { echo "danger"; } ?> pull-right">Required</span>
                </div>   
                <?php if(function_exists('apache_get_modules')){ ?>
                 <div class="col-xs-12">
                   Mod Rewrite Module
                    <span class="label label-<?php if (in_array('mod_rewrite', apache_get_modules())) { echo "success"; } else { echo "danger"; } ?> pull-right">Required</span>
                </div>     
                <?php } ?>
                <div class="col-xs-12">
                   Linux Server
                    <span class="label label-<?php if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') { echo "success"; } else { echo "danger"; } ?> pull-right">Required</span>
                </div>   

          </div>
          <br>
            <legend><i class="fa fa-folder-open"></i> Directory & Permissions</legend>
            <div class="row" style="color:#757575">
               <div class="col-xs-12">
                    Config Folder (public_html/app/config/)
                    <span class="label label-<?php if(is_writable(dirname(__FILE__."/config/"))) { echo "success"; } else { echo "danger"; } ?> pull-right"><?php if(is_writable(dirname(__FILE__."/config/"))) { echo "Writable"; } else { echo "Not Writable"; } ?></span>
                </div>
                 <div class="col-xs-12">
                    Storage Folder (public_html/app/storage/)
                    <span class="label label-<?php if(is_writable(dirname(__FILE__."/storage/"))) { echo "success"; } else { echo "danger"; } ?> pull-right"><?php if(is_writable(dirname(__FILE__."/storage/"))) { echo "Writable"; } else { echo "Not Writable"; } ?></span>
                </div>
                <div class="col-xs-12">
                    1<sup>st</sup> Htaccess File (public_html/.htaccess)
                    <span class="label label-<?php if(file_exists("../.htaccess")) { echo "success"; } else { echo "danger"; } ?> pull-right"><?php if(file_exists("../.htaccess")) { echo "Found in Server"; } else { echo "Not Found"; } ?></span>
                </div>
                <div class="col-xs-12">
                    2<sup>nd</sup> Htaccess File (public_html/public/.htaccess)
                    <span class="label label-<?php if(file_exists("../public/.htaccess")) { echo "success"; } else { echo "danger"; } ?> pull-right"><?php if(file_exists("../public/.htaccess")) { echo "Found in Server"; } else { echo "Not Found"; } ?></span>
                </div>
                <div class="col-xs-12">
                <br>
                <hr>
                   <div class="text-center">
                    Don't worry if all is <span class="label label-success">green</span> :)
                </div>

                  
                </div>
            </div>

        </div>
        <div class="col-sm-6">
        <legend><i class="fa fa-database"></i> Database Settings</legend>
            <div class="form-group">                
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-database"></i></span>
                    <input required type="text" class="form-control" placeholder="DB Host" name="inputDBhost">
                </div>
            </div>
            <div class="form-group">                
                <div class="input-group">      
                    <span class="input-group-addon"><i class="fa fa-th-list"></i></span>              
                    <input required type="text" placeholder="DB Name" class="form-control" name="inputDBname">
                </div>
            </div>
            <div class="form-group">                
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-user"></i></span>              
                    <input required type="text" placeholder="Username Database" class="form-control" name="inputDBusername">
                </div>
            </div>
            <div class="form-group">                
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-asterisk"></i></span>              
                    <input type="password" placeholder="Password Database" class="form-control" name="inputDBpassword">
                </div>
            </div>

            <legend><i class="fa fa-lock"></i> Administrator Settings</legend>
            <div class="form-group">                
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-user"></i></span>              
                    <input required type="text" class="form-control" placeholder="Admin Username" name="username">
                </div>
            </div>
            <div class="form-group">                
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-envelope"></i></span>              
                    <input required type="email" class="form-control" placeholder="Admin Email" name="email">
                </div>
            </div>
            <div class="form-group">                
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-lock"></i></span>              
                    <input required type="password" placeholder="Password Administrator" class="form-control" name="password">
                </div>
            </div>
          
            <div class="form-group">
                <div class="controls">                    
                    <button type="submit" class="btn btn-primary" style="width:100%" name="btn-install" /><i class="fa fa-check-circle"></i> Install</div>
                </div>
            </div>

            <div class="col-xs-12">
            <br>
                <div class="text-muted text-center">
                    Copyright &copy; <?php echo date('Y'); ?> <a class="modal-link" href="http://codecanyon.net/user/binaryzeal/portfolio?ref=BinaryZeal">codecanyon.net</a>
                </div>
            </div>
        </div>
    </form>
    </div>
</div>
</body>
</html>