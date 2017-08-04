{{ Lang::get('words.hello') }}
<br>
{{ $username }}
<br><br>
{{ Lang::get('words.activation-message') }}
<br>
<?php
    $activationURL = URL::to('activation?token='.$activation_token);
?>
<a href="{{ $activationURL }}">{{ $activationURL }}</a>
<br>
<br>
{{ Lang::get('words.regards') }}
<br>
{{ Setting::first()->website_name }}
