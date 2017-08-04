@extends('index')
@section('meta-content')
	<meta name="twitter:card" value="photo"/>
	<meta name="twitter:site" value="{{ '@'.$settings->twitter_page_id }}"/>
	<meta name="twitter:image" value="{{ URL::asset('assets/images/logo.png') }}"/>
	<meta property="og:type" content="blog"/>
	<meta property="og:site_name" content="{{ $settings->website_name }}"/>
	<meta property="og:url" content="{{ URL::to('/') }}"/>
	<meta property="og:title" content="{{ $settings->website_name }} - {{ $settings->website_title }}"/>
	<meta property="og:image" content="{{ URL::asset('assets/images/logo.png') }}"/>
	<meta property="og:description" content="{{ $settings->website_name }} {{ $settings->website_description }}"/>
	<title>{{ $settings->website_name }} - {{ $settings->website_title }}</title>
@stop

@section('content')
    @if($topAlbums->count())
	@endif


	 <div class="container">
                <div class="col-md-9 col-sm-9 col-xs-12">
                <div class="filter-product">
                        <div class="row pf-c">
                        <div class="row pf-c">
                            <div class="col-md-9 col-sm-9 col-xs-9">
                                    <div class="left-side-hd">
                                            <h1> Featured Releases </h1>
                                    </div>
                            </div>
                            <div class="col-md-3 col-sm-3 col-xs-3">
                                <!-- Controls -->
                                {{--<div class="controls pull-right hidden-xs">
                                    <a class="left btn btn" href="#carousel-example"
                                        data-slide="prev"><img src="{{ URL::asset('assets/images/arrow-left.png') }}"/></a><a class="right btn" href="#carousel-example"
                                            data-slide="next"><img src="{{ URL::asset('assets/images/arrow-right.png') }}"/></a>
                                </div>--}}
                            </div>
                        </div>
<div class="slider1">
<?php //$i=0; ?>
	        	@foreach($featuredAlbums as $album)
	            	<div class="slide">
		            	<div class="album">
	                    	<img src="{{ URL::to('uploads/albums/'.$album->location.'/thumb/'.$album->slug.'.jpg') }}" alt="{{ $album->title }}" class="img-responsive">
	                    	<div class="play-album" data-album="{{ $album->slug }}" data-cover="{{ URL::to('uploads/albums/'.$album->location.'/thumb/'.$album->slug.'.jpg') }}" data-album_title="{{ $album->title }}" >
		                		<span class="fa fa-play"></span>
		                	</div>
 <?php //$i++; ?>
		                	<div class="album-info">
		                		<a class="album-title pjax" title="{{ $album->title }}" href="{{ URL::to('album/'.Custom::slugify($album->title).'-'.$album->slug) }}">{{ $album->title }}</a>
		                		<span class="album-artist" title="{{ Lang::get('words.browse-albums-of') }} {{ $album->artist->name }}" style="cursor: default;">{{ $album->artist->name }}</span>
		                		<div class="clearfix"></div>
		                	</div>
		               	</div>
		            </div>
		        @endforeach
	        </div>
                    </div>









                </div>
                <!-- filter-product-->

                <div class="latest pr ">
                    <div class="latest-heading">
                        <h1>Latest Releases </h1>
                    </div>
<?php //var_dump($tracks[0]->slug); ?>
                    @foreach(array_chunk($albums->getCollection()->all(),6) as $row )
                        <div class="row td-r  ">
                   <?php $i=0; ?>
                            @foreach($row as $album)
                                <div class="col-md-2 col-sm-2 col-xs-2 pd-f">
                                        <div class="lats-item ">
                                        <div class="play-album" data-album="{{ $album->slug }}" data-album_title="{{ $album->title }}" data-cover="{{ URL::to('uploads/albums/'.$album->location.'/thumb/'.$album->slug.'.jpg') }}" data-waves="{{ URL::to('uploads/audios/'.$album->location.'/wavefiles/'.$tracks[$i]->slug.'.png') }}">
                                                                        <span class="fa fa-play"></span>
                                                                    </div>
<?php $i++; ?>
                                        <a href="javascript:void(0);" class="music-feature-titles">
                                             <div class="lats-photo">
                                                <img src="{{ URL::to('uploads/albums/'.$album->location.'/thumb/'.$album->slug.'.jpg') }}" alt="{{ $album->title }}"/>
                                            </div>
                                        </a>
                                        <div class="lats-info">
                                                <span class="atist-n-d">
                                                    <a class="album-title pjax" title="{{ $album->title }}" href="{{ URL::to('album/'.Custom::slugify($album->title).'-'.$album->slug) }}">{{ $album->title }}</a>
                                                    <span class="album-artist" title="{{ Lang::get('words.browse-albums-of') }} {{ $album->artist->name }}"  style="cursor: default;">{{ $album->artist->name }}</span>
                                               </span>

                                        </div>


                                    </div>
                                </div>

                            @endforeach
                        </div>
                    @endforeach
                </div>

                <!-- latest prroduct-->

                <div class="pagination-point">
                    <div class="pagination">
                        {{ $albums->links() }}
                    </div>

                </div>
                <!-- pagination-point-->

                </div>

                <?php //echo View::make('partials.top10') ; ?>
                @include('partials.top10')

            </div>
@stop