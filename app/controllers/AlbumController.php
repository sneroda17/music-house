<?php

class AlbumController extends \BaseController {

    /**
     * Display a listing of the resource.
     * GET /post
     *
     * @return Response
     */
    public function index()
    {
        $albums = Album::with('artist', 'categories', 'language')->paginate(20);

        return View::make('admin.album.index', array('albums' => $albums));
    }

    public function indexPublic($filter = null)
    {
        $q = htmlspecialchars(Input::get('q'));
        $q = trim($q);
        if(!empty($q)) {
            $albums = Album::with('artist')->where('title', 'like', '%'.$q.'%')->paginate(24);
            return View::make('search.album', array('albums' => $albums, 'searchKey' => $q));
        } else {
            if($filter == 'top') {
                $albums = Album::leftJoin('album_likes', 'albums.id', '=', 'album_likes.album_id')
                                    ->where('album_likes.created_at', '>=', date('Y-m-d H:i:s', strtotime('-1 month')))
                                    ->groupBy('album_likes.album_id')
                                    ->orderBy(DB::raw('COUNT(album_likes.id)'), 'DESC')
                                    ->orderBy('created_at', 'DESC')
                                    ->select('albums.*')
                                    ->paginate(24);
                $ptitle = "Top Albums";
            } elseif($filter == 'trending') {
                $albums = Album::with('artist')
                                ->where('created_at', '>=', date('Y-m-d H:i:s', strtotime('-1 month')))
                                ->orderBy('views', 'desc')->paginate(24);
                $ptitle = "Trending Albums";
            } elseif($filter == 'popular') {
                $albums = Album::with('artist')
                                ->where('created_at', '>=', date('Y-m-d H:i:s', strtotime('-1 month')))
                                ->orderBy('downloads', 'desc')->paginate(24);
                $ptitle = "Popular Albums";
            }
            else {
                $albums = Album::with('artist')->orderBy('id', 'desc')->paginate(24);
                $ptitle = "All Albums";
            }
            return View::make('album.album', array('ptitle' => $ptitle, 'albums' => $albums));
        }
    }

    /**
     * Show the form for creating a new resource.
     * GET /post/create
     *
     * @return Response
     */

    public function show($slug)
    {
        try{
            $parts = explode('-', $slug);
            $slug = end($parts);
            // print_r($slug);
            //die;
            $album = Album::with('artist', 'categories', 'publisher', 'is_favorite', 'language')->where('slug', $slug)->first();


            if($album) {

                $sameLabelAlbums = Album::with('artist', 'categories', 'publisher', 'is_favorite', 'language')
                    ->where("albums.publisher_id",$album->publisher->id)
                    ->where("albums.id",'!=',$album->id)->paginate(2000);

                $album->increment('views');
                $album->save();
                return View::make('album.index', array('album' => $album,"sameLabelAlbums"=>$sameLabelAlbums));
            }

        }catch (Exception $ex){
            echo "Error-".$ex->getMessage();
        }

    }


    public function create()
    {
        return View::make('admin.album.create');
    }

    /**
     * Store a newly created resource in storage.
     * POST /post
     *
     * @return Response
     */
    public function store()
    {

        $input = Input::all();
        //dd($input);
        $validator = Validator::make(
            $input,
            array(
                'title' => 'required|between:3,100',
                'image' => 'mimes:jpeg,png',
                'imgurl' => 'url',
                'language' => 'required|between:1,200',
                'category' => 'required|between:1,200',
                'artist' => 'required|between:1,200',
                'release' => 'required|between:1,200',
            )
        );

        if ($validator->passes()) {
            $slug = Custom::slugify('', 10);
            while (Album::where('slug', $slug)->first()) {
                $slug = Custom::slugify('', 10);
            }

            $imgurl = Input::get('imgurl');
            $imgurl = trim($imgurl);

            if (!empty($imgurl)) {
                $location = Custom::imgUpload($imgurl, $slug, 'albums', true, true);
            } else {
                $imgFile = Input::file('image');
                $location = Custom::imgUpload($imgFile, $slug, 'albums', true, false);
            }
            
            $language = htmlspecialchars(Input::get('language'));
            $sluglang = Custom::slugify($language);

            $artist = htmlspecialchars_decode(Input::get('artist'));
            $slugartist = Custom::slugify($artist);

            $category = htmlspecialchars_decode(Input::get('category'));
            $slugcat = Custom::slugify($category);

            $album = new Album();

            $savedLang = Language::where('slug', $sluglang)->first();
            if(!$savedLang) {
                $clang = new Language();
                $clang->name = $language;
                $clang->slug = $sluglang;
                $clang->save();
                $album->language_id = $clang->id;
            } else {
                $album->language_id = $savedLang->id;
            }

            $savedArtist = Artist::where('slug', $slugartist)->first();
            if(!$savedArtist) {
                $cartist = new Artist();
                $cartist->name = $artist;
                $cartist->slug = $slugartist;
                $cartist->save();
                $album->artist_id = $cartist->id;
            } else {
                $album->artist_id = $savedArtist->id;
            }
            $savedCat = Category::where('slug', $slugcat)->first();
            if(!$savedCat) {
                $ccat = new Category();
                $ccat->name = $category;
                $ccat->slug = $slugcat;
                $ccat->save();
                $album->category_id = $ccat->id;
            } else {
                $album->category_id = $savedCat->id;
            }

            $album->title = htmlspecialchars(stripcslashes(Input::get('title')));
            $album->release_date = Input::get('release');
            $album->slug = $slug;
            $album->location = $location;
            $album->save();
            return json_encode(array('status' => 'success', 'message' => Lang::get('words.album-created'), 'slug' => $slug));
        } else {
            $message = $validator->messages()->all()[0];
            return json_encode(array('message' => $message, 'status' => 'error'));
        }
    }


    /**
     * Update the specified resource in storage.
     * PUT /post/{id}
     *
     * @param  int  $id
     * @return Response
     */
    public function update()
    {
        $input = Input::all();

        $validator = Validator::make(
            $input,
            array(
                'title' => 'required|between:3,100',
                'language' => 'required',
                'image' => 'mimes:jpeg,png',
                'category' => 'required',
                'artist' => 'required',
                'release' => 'required',
                'slug' => 'required',
            )
        );

        if ($validator->passes()) {
            
            $album = Album::where('slug', $input['slug'])->first();

            if($album)
            {
                $album->title = htmlspecialchars(stripcslashes($input['title']));

                $language = htmlspecialchars_decode($input['language']);
                $sluglang = Custom::slugify($language);

                $artist = htmlspecialchars_decode($input['artist']);
                $slugartist = Custom::slugify($artist);

                $category = htmlspecialchars_decode($input['category']);
                $slugcat = Custom::slugify($category);

                $savedLang = Language::where('slug', $sluglang)->first();
                if(!$savedLang) {
                    $clang = new Language();
                    $clang->name = $language;
                    $clang->slug = $sluglang;
                    $clang->save();
                    $album->language_id = $clang->id;
                } else {
                    $album->language_id = $savedLang->id;
                }

                $savedArtist = Artist::where('slug', $slugartist)->first();
                if(!$savedArtist) {
                    $cartist = new Artist();
                    $cartist->name = $artist;
                    $cartist->slug = $slugartist;
                    $cartist->save();
                    $album->artist_id = $cartist->id;
                } else {
                    $album->artist_id = $savedArtist->id;
                }
                $savedCat = Category::where('slug', $slugcat)->first();
                if(!$savedCat) {
                    $ccat = new Category();
                    $ccat->name = $category;
                    $ccat->slug = $slugcat;
                    $ccat->save();
                    $album->category_id = $ccat->id;
                } else {
                    $album->category_id = $savedCat->id;
                }
            }

            $inputImage = isset($input['image']) ? $input['image'] : '';
            if(!empty($inputImage)) {
                $location = Custom::imgUpload($inputImage, $album->slug, 'albums', true, false);
                $album->location = $location;
            }

            
            $album->release_date = $input['release'];
            //$album->location = $location;
            $album->save();
            $tracks = $album->tracks;
            if($tracks->count()) {
                foreach($tracks as $track) {
                    App::make('TrackController')->writeId3Tags($track->slug);
                }
            }
            return json_encode(array('status' => 'success', 'message' => Lang::get('words.album-updated')));
        } else {
            $message = $validator->messages()->all()[0];
            return json_encode(array('message' => $message, 'status' => 'error'));
        }
    }

    public function albumPlay($slug)
    {
        $album = Album::with('tracks', 'tracks.artist')->where('slug', $slug)->first();
        $dump = '';
        if($album) {
            $tracks = $album->tracks;
            if($tracks->count()) {
                
                foreach($tracks as $track) {
                    $dump .= '<li><a href="'.URL::to("uploads/audios/".$track->location."/".$track->slug.".mp3").'"><span>'.$track->title.'</span> - <i>'.$track->artist->name.'</i></a></li>';
                }
            }
        }

        return $dump;
    }

    public function likedAlbums()
    {
        $albumLikes = AlbumLike::with('album')->where('user_id', Auth::user()->id)->paginate(30);
        return View::make('favorites.album', array('albumLikes' => $albumLikes));
    }

    /**
     * Remove the specified resource from storage.
     * DELETE /post/{id}
     *
     * @param  int  $id
     * @return Response
     */
    public function delete($id)
    {
        $album = Album::with('tracks')->where('slug', $id)->first();
        if($album)
        {
            $mediaDir = 'uploads/albums/';
            $audioDir = 'uploads/audios/';
            $albumImg = $mediaDir.$album->location.'/'.$album->slug.'.jpg';
            $albumThumb = $mediaDir.$album->location.'/thumb/'.$album->slug.'.jpg';

            AlbumLike::where('album_id', $album->id)->delete();
            AlbumsGenre::where('album_id', $album->id)->delete();

            $tracks = $album->tracks;
            
            TrackLike::whereIn('track_id', $tracks->lists('id'))->delete();
            
            PlaylistTrack::whereIn('track_id', $tracks->lists('id'))->delete();

            foreach($tracks as $track) {
                $audioFile = $audioDir.$track->location.'/'.$track->slug.'.mp3';
                $this->deleteFile($audioFile);
                $orgTrack  =$audioDir.$track->location.'/org/'.$track->slug.'.mp3';
                $this->deleteFile($orgTrack);
                $trackWave  =$audioDir.$track->location.'/wavefiles/'.$track->slug.'.png';
                $this->deleteFile($trackWave);
            }

            Track::where('album_id', $album->id)->delete();
            AlbumsGenre::where('album_id', $album->id)->delete();
            $this->deleteFile($albumImg);
            $this->deleteFile($albumThumb);
            
            $album->delete();

            return Redirect::to('admin/album');
            
        }
        else
        {
            return Redirect::to('/');
        }

    }
    public function featured($id){
        $album = Album::where('slug', $id)->first();
        if($album){
            $album->featured = $album->featured?0:1;
            $album->save();
            //return json_encode(array('status' => 'success', 'message' => Lang::get('words.album-created'), 'slug' => $id));
            return Redirect::to('admin/album');
        }


    }

    private function deleteFile($file) {
        if(file_exists($file))
        {
            unlink($file);
            return true;
        }
    }

    public function beforeDownload($slug){

        try{

            $settings = Setting::first();
            if($settings->zip_download) {
                $album = Album::with('tracks')->where('slug', $slug)->first();
                if($album) {

                    $tracks = $album->tracks;
                    //dd($tracks);
                    if($tracks->count()) {
                        if(extension_loaded('zip')) {
                            $zip = new ZipArchive(); // Load zip library

                            //dd($zip);
                            $zip_name = Custom::slugify($album->title).".zip"; // Zip name
                            if($zip->open($zip_name, ZIPARCHIVE::CREATE)!==false) {

                                $albumTotal = 0 ;
                                foreach($tracks as $track) {
                                    $file = 'uploads/audios/'.$track->location.'/'.$track->slug.'.mp3';
                                    //$size = filesize($file);

                                    $albumTotal = $albumTotal + filesize($file);

                                    $zip->addFile($file, $album->title.'/'.$track->title.'['.$settings->website_name.'].mp3'); // Adding files into zip
                                }
                                $zip->close();

                                $user = Auth::user();

                                if($user) {

                                    if( count($user->subscriber) != 0 ) {

                                        //User Time limit
                                        $timeLimit = strtotime($user->subscriber->time_limit);

                                        //dd($timeLimit);

                                        $currentTime = strtotime(date('j-m-Y', time()));

                                        //User Download data left
                                        $downloadsLeft = $user->subscriber->download_limit - $user->subscriber->downloaded_data;

                                        //Downloading file size
                                        $fileSize = round($albumTotal/1048576);



                                        //dd($fileSize);

                                        if( $timeLimit >= $currentTime && $downloadsLeft >= $fileSize ) {

                                            return json_encode(array('message' => 'Access denied.', 'status' => 'success'));

                                        }else{

                                            return json_encode(array('message' => 'You need to upgrade your subscription.', 'status' => 'error'));
                                        }
                                    }else{
                                        return json_encode(array('message' => 'You need to subscribe first.', 'status' => 'error'));
                                    }

                                }else{
                                    return json_encode(array('message' => 'You need to Login', 'status' => 'error'));
                                }
                            }

                        }
                    }

                }
            }

        }catch (Exception $ex){
            echo $ex->getMessage();
        }
    }

    public function download($slug){

        try{

            $settings = Setting::first();
            if($settings->zip_download) {
                $album = Album::with('tracks')->where('slug', $slug)->first();
                if($album) {
                    $tracks = $album->tracks;
                    if($tracks->count()) {
                        if(extension_loaded('zip')) {
                            $zip = new ZipArchive(); // Load zip library
                            $zip_name = Custom::slugify($album->title).".zip"; // Zip name
                            if($zip->open($zip_name, ZIPARCHIVE::CREATE)!==false) {
                                $albumTotal = 0 ;
                                foreach($tracks as $track) {
                                    $file = 'uploads/audios/'.$track->location.'/'.$track->slug.'.mp3';
                                    //$size = filesize($file);

                                    $albumTotal += filesize($file);

                                    $zip->addFile($file, $album->title.'/'.$track->title.'['.$settings->website_name.'].mp3'); // Adding files into zip
                                }
                                $zip->close();

                                //dd($albumTotal);

                                $user = Auth::user();

                                if($user) {

                                    if( count($user->subscriber) != 0 ) {

                                        //User Time limit
                                        $timeLimit = strtotime($user->subscriber->time_limit);

                                        $currentTime = strtotime(date('j-m-Y', time()));

                                        //User Download data left
                                        $downloadsLeft = $user->subscriber->download_limit - $user->subscriber->downloaded_data;

                                        //Downloading file size
                                        $fileSize = round($albumTotal/1048576);

                                        //dd([$timeLimit, $currentTime, $downloadsLeft]);

                                        if( $timeLimit >= $currentTime && $downloadsLeft >= $fileSize ) {

                                            if (file_exists($zip_name)) {
                                                // push to download the zip
                                                header('Content-type: application/zip');
                                                header('Content-Disposition: attachment; filename="' . $zip_name . '"');
                                                readfile($zip_name);
                                                // remove zip file is exists in temp path
                                                unlink($zip_name);
                                            }

                                            //dd($user);
                                            $album->increment('downloads');
                                            $album->save();

                                            //Adding downloading data to user downloaded data
                                            $userId = Auth::user()->id;

                                            $user = User::find($userId)->subscriber;
                                            $user->downloaded_data = $user->downloaded_data + $fileSize;

                                            $user->save();


                                        }else{

                                            return json_encode(array('message' => 'You need to upgrade your subscription.', 'status' => 'error'));
                                        }
                                    }else{
                                        return json_encode(array('message' => 'You need to subscribe first.', 'status' => 'error'));
                                    }

                                }else{
                                    return json_encode(array('message' => 'You need to login.', 'status' => 'error'));
                                }
                            }

                        }
                    }

                }
            }

        }catch (Exception $ex){
            echo $ex->getMessage();
        }

    }

}