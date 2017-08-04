@extends('index')
@section('meta-content')
	<?php $albumImg =  URL::to('uploads/albums/'.$album->location.'/'.$album->slug.'.jpg'); ?>
	<meta name="twitter:card" value="photo"/>
	<meta name="twitter:site" value="{{ '@'.$settings->twitter_page_id }}"/>
	<meta name="twitter:image" value="{{ $albumImg }}"/>
	<meta property="og:type" content="article" />
	<meta property="og:site_name" content="{{ $settings->website_name }}"/>
	<meta property="og:url" content="{{ Request::url() }}"/>
	<meta property="og:title" content="{{ Lang::get('words.download') }} {{ $album->title }} {{ Lang::get('words.tracks') }}"/>
	<meta property="og:image" content="{{ $albumImg }}"/>
	<meta property="og:description" content="{{ Lang::get('words.download') }} {{ $album->title }} {{ Lang::get('words.tracks') }}"/>
	<title>{{ Lang::get('words.download') }} {{ $album->title }} {{ Lang::get('words.tracks') }}</title>
@stop

@section('content')
<?php
	$tracks = $album->tracks()->with('artist', 'is_favorite')->paginate(20);
	$tl_counts = $album->getPopularity($tracks->lists('id'))->get();
	$tl_array = array();
	foreach($tl_counts as $tl_count) {
		$tl_array[$tl_count->track_id] = $tl_count->count;
	}
	$downloadLink = '';
	if($settings->downloadable) {
		$downloadLink = '<button title="'.Lang::get("words.download").'" class="btn btn-transparent btn-circle track-download"><span class="fa fa-download"></span></button>';
	}
?>

<div class="release_full-page">
    <div class="container">
        <div class="rels-form">
            <div class="col-md-12">
                <div class="col-md-3">
                    <div class="img-music-rel">
                    <img src="{{ URL::to('uploads/albums/'.$album->location.'/thumb/'.$album->slug.'.jpg') }}" />
                    </div>  <!--img-music-rel-->
                </div>
                <div class="col-md-9">
                    <div div class="title-releplay-albums">
                        <div class="title-name-reles">
                            <p>Release:<span> {{ $album->title }}	</span> </p>
                            <p>{{ Lang::get('words.artist') }} :<span class="tt-artist" > 
                            <?php

                                $artistList = [];
                                foreach($tracks as $track){
                                     $artistList[] = $track->artist->name;
                                }
                                $list = array_unique($artistList);

                                for($i=0; $i<count($list); $i++){
                                   echo $list[$i];
                                }

                            ?>
                            </span> </p>
                            <p>Genre :<span>
                                @for($i=0; $i<count($album->categories); $i++)

                                    {{ $album->categories[$i]->name }},

                                @endfor
                            </span> </p>
                            <p>Label :<span>{{$album->publisher->name}}</span> </p>
                            <p>Added On :<span> {{ $album->release_date }} 	</span> </p>
                            <div class="button-play-relaease" id="bt-play" >
                            <a href="javascript:void(0)" class="play-album-release"  data-album="{{ $album->slug }}" data-cover="{{ URL::to('uploads/albums/'.$album->location.'/thumb/'.$album->slug.'.jpg') }}" data-album_title="{{ $album->title }}" data-waves="{{ URL::to('uploads/audios/'.$album->location.'/wavefiles/'.$track->slug.'.png') }}" >
                                 <img src="{{ URL::to('assets/images/play_button.png')}}" />
                            </a>

                            </div>

                        </div>
                    </div>  <!--title-reles-->
                </div>
            </div>

            <div class="col-md-12">
                <table class="table album-tracks" style="margin-top: 20px;">
            			<thead>
            				<tr>
            					<th>{{ Lang::get('words.title') }}</th>
            					<th class="hidden-480">{{ Lang::get('words.duration') }}</th>
            					<th class="hidden-480">{{ Lang::get('words.size') }}</th>
            					<th class="hidden-480">{{ Lang::get('words.genre') }}</th>
            					<th class="text-center">{{ Lang::get('words.actions') }}</th>
            				</tr>
            			</thead>
            			<tbody class="scroll-container">
            			@foreach($tracks as $track)
            				<?php


            					$track_likes = isset($tl_array[$track->id]) ? $tl_array[$track->id] : 1;
            					$likepercent = Custom::popularity($track_likes);
            					$likepercent = $likepercent < 10 ? 10 : $likepercent;

            					/*try{
                                    $getID3 = new getID3;
                                        $mp3fileInfo = $getID3->analyze((string)('uploads/audios/'.$track->location.'/'.$track->slug.'.mp3'));
                                        $genre = $mp3fileInfo['id3v2']['comments']['genre'][0];
                                        //print_r($mp3fileInfo);
                                    }catch (Exception $ex){
                                        echo $ex->getMessage();
                                    }*/
            				?>

            				<tr class="scroll-items" data-title="{{ $track->title }}" data-trackid="{{ $track->id }}" data-artist="{{ $track->artist->name }}" data-slug="{{ $track->slug }}" data-track="{{ URL::to('uploads/audios/'.$track->location.'/'.$track->slug.'.mp3') }}" data-cover="{{ URL::to('uploads/albums/'.$album->location.'/thumb/'.$album->slug.'.jpg') }}" data-album_title="{{ $album->title }}">
            					<td>
            						<button class="btn btn-transparent btn-circle pull-left track-play">
            							<span class="fa fa-play"></span>
            						</button>
            						<div class="track-album pull-left">
            							<!--<a class="tt-title" title="{{ Lang::get('words.download').' '.$track->title }}" href="{{ URL::to('track/'.Custom::slugify($track->title).'-'.$track->slug) }}" >{{ $track->title }}</a>
            							<a class="tt-artist" title="{{ Lang::get('words.browse-albums', array('title' => $track->artist->name)) }}" href="{{ URL::to('artist/'.$track->artist->slug) }}">{{ $track->artist->name }}</a> -->

            							<span class="tt-title" title="{{ Lang::get('words.download').' '.$track->title }}" >{{ $track->title }}</span>
                                        <span class="tt-artist" title="{{ Lang::get('words.browse-albums', array('title' => $track->artist->name)) }}" >{{ $track->artist->name }}</span>
            						</div>
            						<div class="clearfix"></div>
            					</td>
            					<td class="hidden-480">
            						{{ Custom::formatMilliseconds($track->duration) }}
            					</td>
            					<td class="hidden-480">
            						{{ Custom::formatBytes($track->filesize) }}
            					</td>
            					<td class="hidden-480">
            						<!--<span class="rating-wrap">
            							<span class="rating-bar" style="width:{{ $likepercent }}%"></span>
          						</span>-->
{{$track->category->name}}
            					</td>
            					<td class="text-center">
            						{{ $downloadLink }}
            						<button title="{{ Lang::get('words.add-to-playlist') }}" class="btn btn-transparent btn-circle" data-toggle="modal" data-target="#track-playlist-modal"><span class="fa fa-plus"></span></button>
            						<button title="{{ Lang::get('words.favorite') }}" class="btn btn-transparent btn-circle track-like @if($track->is_favorite->count()) active @endif"><span class="fa fa-heart"></span></button>
            					</td>
            				</tr>
            			@endforeach
            			</tbody>
            			{{ $tracks->links('pagination::simple') }}
            		</table>
            		@if($settings->zip_download)
            		<div class="download-file">
                            <button class="btn btn-labeled btn-danger album-download" data-album="{{ $album->slug }}"><span class="btn-label"><i class="fa fa-download"></i></span> {{ Lang::get('words.download') }}</button>@endif
                    </div>

            </div>
        </div>  <!--rels-form-->
    </div>
    @if($sameLabelAlbums->count())
        <div class="container">
        <div class="more-label">
            <div class="row pf-c">
            <div class="row pf-c">
                <div class="col-md-9">
                    <div class="h-b-most">
                    <h3> More From This Label </h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <!-- Controls -->
                    <div class="controls pull-right hidden-xs">
                        <a class="left bt-ar" href="#carousel-example"
                            data-slide="prev"><img src="{{ URL::to('assets/images/arrow-1-left.png')}}"></a><a class="right bt-ar" href="#carousel-example"
                                data-slide="next"><img src="{{ URL::to('assets/images/arrow-1-right.png')}}"></a>
                    </div>
                </div>
            </div>

            <div id="carousel-example" class="carousel slide hidden-xs" data-ride="carousel">
                <!-- Wrapper for slides -->
                <div class="carousel-inner">
                @foreach(array_chunk($sameLabelAlbums->getCollection()->all(),6) as $row )
                    <div class="item active">
                        <div class="row pf-c">
                            @foreach($row as $album)
                                <div class="col-sm-2 pd-f">
                                    <div class="most-list">
                                    <a href="javascript:void(0);" class="music-feature-titles">
                                         <div class="photo">
                                            <img src="{{ URL::to('uploads/albums/'.$album->location.'/thumb/'.$album->slug.'.jpg') }}" class="img-responsive" alt="a"  alt="{{ $album->title }}"/>
                                        </div>
                                        </a>
                                        <div class="most-info">
                                            <p>
                                                <a class="album-title pjax" title="{{ $album->title }}" href="{{ URL::to('album/'.Custom::slugify($album->title).'-'.$album->slug) }}">{{ $album->title }}</a>
                                                    <a class="album-artist pjax" title="{{ Lang::get('words.browse-albums-of') }} {{ $album->artist->name }}" href="{{ URL::to('artist/'.$album->artist->slug) }}">{{ $album->artist->name }}</a>
                                            </p>


                                        </div>

                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
                </div>
            </div>
        </div>
        </div>

</div>
    @else
    <div class="container">
            <div class="more-label">
                <div class="row pf-c">
                <div class="row pf-c">
                    <div class="col-md-9">
                        <div class="h-b-most">
                        <h3> No More From This Label </h3>
                        </div>
                    </div>
                </div>
            </div>
         </div>
      </div>


    @endif
</div>
@stop