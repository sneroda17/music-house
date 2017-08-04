@extends('index')
@section('meta-content')
	<meta name="twitter:card" value="photo"/>
	<meta name="twitter:site" value="{{ '@'.$settings->twitter_page_id }}"/>
	<meta name="twitter:image" value="{{ URL::asset('assets/images/logo.png') }}"/>
	<meta property="og:type" content="blog"/>
	<meta property="og:site_name" content="{{ $settings->website_name }}"/>
	<meta property="og:url" content="{{ Request::url() }}"/>
	<meta property="og:title" content="{{ $settings->website_name }} - {{ $searchKey }} {{ Lang::get('words.albums') }}"/>
	<meta property="og:image" content="{{ URL::asset('assets/images/logo.png') }}"/>
	<meta property="og:description" content="{{ $settings->website_name }} - {{ $searchKey }} {{ Lang::get('words.albums') }}"/>
	<title>{{ $settings->website_name }} - {{ $searchKey }} {{ Lang::get('words.albums') }}</title>
@stop

@section('content')

<div class="container">
                <div class="col-md-9 col-sm-9 col-xs-12">
                <!-- filter-product-->
                <div class="latest pr ">
                <div class="latest-heading">
                       <h2> <span class="text-muted">{{ Lang::get('words.search') }}</span> <a class="fav-link pjax" href="{{ URL::to('/?q='.$searchKey) }}" title="{{ Lang::get('words.tracks') }}">{{ Lang::get('words.tracks') }}</a> <a class="fav-link active pjax" href="{{ URL::to('album?q='.$searchKey) }}" title="{{ Lang::get('words.albums') }}">{{ Lang::get('words.albums') }}</a>{{-- <a class="fav-link pjax" href="{{ URL::to('artist?q='.$searchKey) }}" title="{{ Lang::get('words.artists') }}">{{ Lang::get('words.artists') }}</a>--}}</h2>
                </div>

                    @foreach(array_chunk($albums->getCollection()->all(),6) as $row )
                                <div class="row td-r  ">
                                    @foreach($row as $index => $album)
                                    <?php $tracks_count = isset($aa_array[$album->id]) ? $aa_array[$album->id] : 0; ?>
                                                        <div class="col-md-2 col-sm-2 col-xs-2 pd-f">
                                                                <div class="lats-item ">
                                                                <div class="play-album" data-album="{{ $album->slug }}" data-album_title="{{ $album->title }}" data-cover="{{ URL::to('uploads/albums/'.$album->location.'/thumb/'.$album->slug.'.jpg') }}">
                                                                		                		<span class="fa fa-play"></span>
                                                                		                	</div>
                                                                <a href="javascript:void(0);" class="music-feature-titles">
                                                                     <div class="lats-photo">
                                                                        <img src="{{ URL::to('uploads/albums/'.$album->location.'/thumb/'.$album->slug.'.jpg') }}" alt="{{ $album->title }}"/>
                                                                    </div>
                                                                </a>
                                                                <div class="lats-info">
                                                                        <span class="atist-n-d">
                                                                            <a class="album-title pjax" title="{{ $album->title }}" href="{{ URL::to('album/'.Custom::slugify($album->title).'-'.$album->slug) }}">{{ $album->title }}</a>
                                                                            <span class="album-artist" title="{{ Lang::get('words.browse-albums-of') }} {{ $album->artist->name }}" style="cursor: default;">{{ $album->artist->name }}</span>
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

 <?php echo View::make('partials.top10') ; ?>



            </div>
@stop