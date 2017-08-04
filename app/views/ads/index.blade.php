<!DOCTYPE html>
<html>
<head>
	<title>
        {{ $settings->website_name }} - {{ $settings->website_title }}
    </title>
    <meta charset="UTF-8">
    <meta name="description" content="{{ $settings->website_description }}">
    <meta name="keywords" content="{{ $settings->website_keywords }}">
    <meta name="author" content="{{ $settings->website_name }}">

    <meta name="twitter:card" content="photo" />
    <meta name="twitter:site" content="{{ $settings->twitter_page_id }}" />
    <meta name="twitter:image" content="{{ URL::asset('assets/images/logo.png') }}"/>

    <meta property="og:site_name" content="{{ $settings->website_name }}" />
    <meta property="og:type" content="article" />
    <meta property="og:title" content="{{ $settings->website_name }} - {{ $settings->website_title }}"/>
    <meta property="og:image" content="{{ URL::asset('assets/images/logo.png') }}"/>
    <meta property="og:description" content="{{ $settings->website_description }}"/>

    <meta itemprop="name" content="{{ $settings->website_name }} - {{ $settings->website_title }}">
    <meta itemprop="description" content="{{ $settings->website_description }}">
    <meta itemprop="image" content="{{ URL::asset('assets/images/logo.png') }}">
</head>
<body>
@if($type == 'banner')
    {{ htmlspecialchars_decode($settings->banner_ad) }}
@else($type == 'box')
    {{ htmlspecialchars_decode($settings->box_ad) }}
@endif
</body>
</html>
