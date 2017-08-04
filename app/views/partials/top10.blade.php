<?php
/**
 * Created by PhpStorm.
 * User: sanjay
 * Date: 10-Apr-17
 * Time: 4:06 PM
 */
 ?>
<div class="col-md-3 col-sm-3 col-xs-12 right-les">
        <div class="right-site-top">
            <div class="top-trak-list">
                    <h3>Top 10 Tracks </h3>
            </div>
          <?php $i=0; ?>
            @foreach($popularAlbums as $index => $album)

                <div class="col-md-12 rit-t">
                    <div class="top-item">
                        <div class="num-1">
                                <strong>{{$index+1}}</strong>
                        </div>
                        <div class="top-item-1 album">
                            <div class="play-album" data-album="{{ $album->slug }}" data-album_title="{{ $album->title }}" data-cover="{{ URL::to('uploads/albums/'.$album->location.'/thumb/'.$album->slug.'.jpg') }}" >
                                <span class="fa fa-play"></span>
                            </div>
                    <?php $i++; ?>
                            <img src="{{ URL::to('uploads/albums/'.$album->location.'/thumb/'.$album->slug.'.jpg') }}" alt="{{ $album->title }}">
                         </div>




                        <div class="top-item-2">
                                 <span class="atist-n-q">
                                          <a class="album-title pjax" title="{{ $album->title }}" href="{{ URL::to('album/'.Custom::slugify($album->title).'-'.$album->slug) }}">{{ $album->title }}</a>
                                          <a href="javascript:void(0);" class="top10A" >{{ $album->artist->name }}</a>
                                          <a href="javascript:void(0)" class="top10A" ><span>{{$album->publisher->name}}</span></a>
                                 </span>
                        </div>
                    </div>
            </div>
            @endforeach


        </div>
</div>