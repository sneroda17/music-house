<?php
require_once(dirname(__File__).'/../libs/getid3/getid3.php');
require_once(dirname(__File__).'/../libs/getid3/write.php');

class TrackController extends \BaseController {

    /**
     * Display a listing of the resource.
     * GET /pages
     *
     * @return Response
     */
    public function index()
    {
        $trackLikes = TrackLike::with('track')->where('user_id', Auth::user()->id)->paginate(30);
        return View::make('favorites.index', array('trackLikes' => $trackLikes));
    }

    public function showTrack($slug) {
        $parts = explode('-', $slug);
        $slug = end($parts);

        $track = Track::with('album', 'artist', 'is_favorite', 'album.is_favorite')->where('slug', $slug)->first();

        if($track) {
            return View::make('track.index', array('track' => $track));
        }

    }

    /**
     * Show the form for creating a new resource.
     * GET /pages/create
     *
     * @return Response
     */
    public function create($id)
    {
        $album = Album::with('artist', 'category', 'language', 'tracks', 'tracks.artist')->where('slug', $id)->first();
        if($album)
        {
            return View::make('admin.album.add', array('album' => $album));
        }
    }

    /**
     * Store a newly created resource in storage.
     * POST /pages
     *
     * @return Response
     */
    public function store($id)
    {
        $album = Album::where('slug', $id)->first();
        if($album)
        {
            $input = Input::all();
            $validator = Validator::make($input, array(
                    'title' => 'required|between:3,200',
                    'audio' => 'mimes:mpga',
                    'artist' => 'required|between:1,200',
                )
            );
            if ($validator->passes()) {
                $slug = Custom::slugify('', 10);
                while (Track::where('slug', $slug)->first()) {
                    $slug = Custom::slugify('', 10);

                }
                $track = new Track();
                $input['artist'] = htmlspecialchars($input['artist']);
                $slugartist = Custom::slugify($input['artist']);
                $savedArtist = Artist::where('slug', $slugartist)->first();
                if(!$savedArtist) {
                    $cartist = new Artist();
                    $cartist->name = $input['artist'];
                    $cartist->slug = $slugartist;
                    $cartist->save();
                    $track->artist_id = $cartist->id;
                } else {
                    $track->artist_id = $savedArtist->id;
                }
                $track->title = htmlspecialchars(stripslashes($input['title']));
                $track->location = Custom::uploadAudio($input['audio'], $slug);
                $track->slug = $slug;
                $track->album_id = $album->id;

                $getID3 = new getID3;
                $ThisFileInfo = $getID3->analyze('uploads/audios/'.$track->location.'/'.$track->slug.'.mp3');
                getid3_lib::CopyTagsToComments($ThisFileInfo);
                $track->duration = abs($ThisFileInfo['playtime_seconds'])*1000;
                $track->filesize = abs($ThisFileInfo["filesize"]);


                $track->save();
                $this->writeId3Tags($track->slug);
                return json_encode(array('status' => 'success', 'message' => Lang::get('words.track-added'), 'slug' => $id));
            } else {
                $message = $validator->messages()->all()[0];
                return json_encode(array('message' => $message, 'status' => 'error'));
            }

        }
    }
    public function createBulkAlbum($albumData){
        try{
            $slug = Custom::slugify('', 10);
            while (Album::where('slug', $slug)->first()) {
                $slug = Custom::slugify('', 10);
            }

            $language = htmlspecialchars($albumData['language']);
            $sluglang = Custom::slugify($language);

            $artist = htmlspecialchars_decode($albumData['artist']);
            $slugartist = Custom::slugify($artist);

            $category = htmlspecialchars_decode($albumData['genre']);
            $slugcat = Custom::slugify($category);

            $publisher = htmlspecialchars_decode($albumData['publisher']);
            $slugPub = Custom::slugify($publisher);


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

            /*$savedCat = Category::where('slug', $slugcat)->first();
            if(!$savedCat) {
                $ccat = new Category();
                $ccat->name = $category;
                $ccat->slug = $slugcat;
                $ccat->save();
                $album->category_id = $ccat->id;
            } else {
                $album->category_id = $savedCat->id;
            }*/

            $publisher = htmlspecialchars_decode($albumData['publisher']);
            $slugPub = Custom::slugify($publisher);

            $savedPub = Publisher::where('slug', $slugPub)->first();
            if(!$savedPub) {
                $pubObj = new Publisher();
                $pubObj->name = $publisher;
                $pubObj->slug = $slugPub;
                $pubObj->save();
                $album->publisher_id = $pubObj->id;
            } else {
                $album->publisher_id = $savedPub->id;
            }



            $Image='data:'.$albumData['imageMime'].';charset=utf-8;base64,'.base64_encode($albumData['imageData']);
            $location = Custom::imgUploadBase64($Image,$slug);

            $album->title = htmlspecialchars(stripcslashes($albumData['albumTitle']));
            $album->release_date = $albumData['release_time'];
            $album->slug = $slug;
            $album->location = $location;
            $album->category_id = 0;
            $album->save();

            /*$countAlbumGenre= AlbumsGenre::where("album_id",$album->id)
                ->where("category_id",$album->category_id)->count();

            if(!$countAlbumGenre){
                $objAlbumGenre = new \AlbumsGenre();
                $objAlbumGenre->album_id = $album->id;
                $objAlbumGenre->category_id = $album->category_id;
                $objAlbumGenre->save();
            }*/
            return $album->id;
        }
        catch(Exception $e){
            DB::rollback();
            $message= $e->getMessage();   // insert query
            return json_encode(array('status' => 'error', 'message' => $message));
        }

    }
    public function uploadBulk(){
        return View::make('admin.bulkupload');
    }

    public function startUploadBulk()
    {



        try{
            //$directory = "files";
            $input = Input::all();
            $directory = $input['path'];
            $files = File::allFiles($directory);
            $i=0;
            /*foreach ($files as $file)
            {
                $extension = File::extension((string)$file);
                if($extension==="mp3"){
                    $getID3 = new getID3;
                    $ThisFileInfo = $getID3->analyze((string)$file);
                    echo '--------------------------------<br>';
                    echo $ThisFileInfo['id3v2']['comments']['title'][0].'<br>';
                    echo '<pre>';
                    print_r($ThisFileInfo['id3v2']['comments']['genre']);
                    echo '</pre>';
                    echo '--------------------------------<br>';
                }
            }
            die;*/
            /*$album = Album::with('artist', 'category', 'language')->orderBy('created_at', 'desc')->paginate(20);

            echo '<pre>';
            print_r($album);
            echo '</pre>';
            die;*/
            foreach ($files as $file)
            {
                $extension = File::extension((string)$file);

                if($extension==="mp3"){
                    DB::beginTransaction();
                    //$file_ = new Symfony\Component\HttpFoundation\File\File((string)$file);
                    //$mime = $file_->getMimeType();
                    //echo $mime.' File-'.(string)$file.'<br>';
                    //if($i<=2) {
                    $getID3 = new getID3;
                    $ThisFileInfo = $getID3->analyze((string)$file);
                    $catSlug = ($ThisFileInfo['id3v2']['comments']['album'][0])?htmlspecialchars(stripcslashes($ThisFileInfo['id3v2']['comments']['album'][0])):'';
                    $albumTitle =  $ThisFileInfo['id3v2']['comments']['album'][0];
                    $imageData = isset($ThisFileInfo['id3v2']['APIC'][0]['data'])?$ThisFileInfo['id3v2']['APIC'][0]['data']:'';
                    $mime = isset($ThisFileInfo['id3v2']['APIC'][0]['image_mime'])?$ThisFileInfo['id3v2']['APIC'][0]['image_mime']:'';
                    $artist = isset($ThisFileInfo['id3v2']['comments']['artist'][0])?$ThisFileInfo['id3v2']['comments']['artist'][0]:'';
                    $genre = isset($ThisFileInfo['id3v2']['comments']['genre'][0])?$ThisFileInfo['id3v2']['comments']['genre'][0]:'';
                    $initial_key = isset($ThisFileInfo['id3v2']['comments']['initial_key'][0])?$ThisFileInfo['id3v2']['comments']['initial_key'][0]:'';
                    $original_year = isset($ThisFileInfo['id3v2']['comments']['original_year'][0])?$ThisFileInfo['id3v2']['comments']['original_year'][0]:0;
                    $publisher = isset($ThisFileInfo['id3v2']['comments']['publisher'][0])?$ThisFileInfo['id3v2']['comments']['publisher'][0]:'';
                    $title = isset($ThisFileInfo['id3v2']['comments']['title'][0])?$ThisFileInfo['id3v2']['comments']['title'][0]:'';
                    $release_time = isset($ThisFileInfo['id3v2']['comments']['release_time'][0])?$ThisFileInfo['id3v2']['comments']['release_time'][0]:'';
                    $language = isset($ThisFileInfo['id3v2']['COMM'][0]['languagename'])?$ThisFileInfo['id3v2']['COMM'][0]['languagename']:'';
                    $album = Album::where('title', $catSlug)->first();
                    if (!$album) {
                        $albumData = array("albumTitle" => $albumTitle,
                            "imageData" => $imageData,
                            "imageMime" => $mime,
                            "artist" => $artist,
                            "genre" => $genre,
                            "initial_key" => $initial_key,
                            "original_year" => $original_year,
                            "publisher" => $publisher,
                            "release_time" => $release_time,
                            "language" => $language
                        );
                        $albumId = $this->createBulkAlbum($albumData);
                    } else {
                        $albumId = $album->id;
                    }
                    $category = htmlspecialchars_decode($genre);
                    $slugcat = Custom::slugify($category);

                    $savedCat = Category::where('slug', $slugcat)->first();
                    $catId='';
                    if(!$savedCat) {
                        $ccat = new Category();
                        $ccat->name = $category;
                        $ccat->slug = $slugcat;
                        $ccat->save();
                        $catId = $ccat->id;
                    } else {
                        $catId = $savedCat->id;
                    }
                    $countAlbumGenre= AlbumsGenre::where("album_id",$albumId)
                        ->where("category_id",$catId)->count();

                    if(!$countAlbumGenre){
                        $objAlbumGenre = new \AlbumsGenre();
                        $objAlbumGenre->album_id = $albumId;
                        $objAlbumGenre->category_id = $catId;
                        $objAlbumGenre->save();
                    }

                    /*$Image='data:'.$mime.';charset=utf-8;base64,'.base64_encode($imageData);
                    $info = Custom::imgUploadBase64(($Image));
                    echo $info;*/

                    /*------------Add Track Creation---------------*/
                    $slug = Custom::slugify('', 10);
                    while (Track::where('slug', $slug)->first()) {
                        $slug = Custom::slugify('', 10);

                    }
                    $track = new Track();

                    $slugartist = Custom::slugify($artist);

                    $savedArtist = Artist::where('slug', $slugartist)->first();
                    if (!$savedArtist) {
                        $cartist = new Artist();
                        $cartist->name = $artist;
                        $cartist->slug = $slugartist;
                        $cartist->save();
                        $track->artist_id = $cartist->id;
                    } else {
                        $track->artist_id = $savedArtist->id;
                    }



                    $track->title = htmlspecialchars(stripslashes($title));
                    $track->location = Custom::bulkUploadAudio((string)$file, $slug);
                    $fileConverted =  'uploads/audios/' . $track->location . '/'.$slug.".mp3";
                    $track->slug = $slug;
                    $track->album_id = $albumId;
                    $track->category_id = $catId;  //genre
                    getid3_lib::CopyTagsToComments($ThisFileInfo);
                    $track->duration = abs($ThisFileInfo['playtime_seconds']) * 1000;
                    $track->filesize = abs($ThisFileInfo["filesize"]);
                    //$track->filesize = abs($fileSize);
                        $track->save();
                    //$this->writeId3Tags($track->slug);
                    DB::commit();
                }
                /*------------Add Track Creation---------------*/
            }

        }
        catch(Exception $e){
            // do task when error
            DB::rollback();
            $message= $e->getMessage();   // insert query
            return json_encode(array('status' => 'error', 'message' => $message));
        }
        return json_encode(array('status' => 'success', 'message' => Lang::get('words.bulk-upload-success')));
    }

    /**
     * Display the specified resource.
     * GET /pages/{id}
     *
     * @param  int  $id
     * @return Response
     */
    public function show($title)
    {
        $page = Page::where('title', '=', $title)->first();
        if ($page) {
            return View::make('pages.index', array('page' => $page));
        } else {
            return Redirect::to('404');
        }
    }

    /**
     * Show the form for editing the specified resource.
     * GET /pages/{id}/edit
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     * PUT /pages/{id}
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
                'title'     => 'required|between:3,100',
                'artist'    => 'required',
                'slug'      => 'required',
            )
        );

        if ($validator->passes()) {
            $track = Track::where('slug', $input['slug'])->first();

            if($track) {
                $track->title = htmlspecialchars(stripcslashes($input['title']));

                $artist = htmlspecialchars($input['artist']);
                $slugartist = Custom::slugify($artist);

                $savedArtist = Artist::where('slug', $slugartist)->first();
                if(!$savedArtist) {
                    $cartist = new Artist();
                    $cartist->name = $artist;
                    $cartist->slug = $slugartist;
                    $cartist->save();
                    $track->artist_id = $cartist->id;
                } else {
                    $track->artist_id = $savedArtist->id;
                }
                $track->save();
                $this->writeId3Tags($track->slug);
                return json_encode(array('status' => 'success', 'message' => 'Track Updated', 'slug' => $track->album->slug));
            }
        } else {
            $message = $validator->messages()->all()[0];
            return json_encode(array('message' => $message, 'status' => 'error'));
        }
    }

    /**
     * Remove the specified resource from storage.
     * DELETE /pages/{id}
     *
     * @param  int  $id
     * @return Response
     */
    public function delete($id)
    {
        $track = Track::where('slug', $id)->first();
        if($track)
        {
            TrackLike::where('track_id', $track->id)->delete();

            PlaylistTrack::where('track_id', $track->id)->delete();

            $audioFile = 'uploads/audios/'.$track->location.'/'.$track->slug.'.mp3';
            if(file_exists($audioFile)) {
                unlink($audioFile);
            }
            $track->delete();
            return Redirect::to('admin/album');
        }
    }

    public function writeId3Tags($slug)
    {
        if(!empty($slug)) {
            $track = Track::with('album', 'artist')->where('slug', $slug)->first();

            if($track) {
                $settings = Setting::first();
                $artist = $track->artist->name;
                $album = $track->album->title;
                $comment = $settings->website_name . ' :: ' . $settings->website_title;
                $cover = 'uploads/albums/'.$track->album->location.'/'.$track->album->slug.'.jpg';
                $filename = 'uploads/audios/'.$track->location.'/'.$track->slug.'.mp3';
                $title = $track->title;

                $TextEncoding = 'UTF-8';
                // Initialize getID3 engine
                $getID3 = new getID3;
                $getID3->setOption(array('encoding'=>$TextEncoding));

                // Initialize getID3 tag-writing module
                $tagwriter = new getid3_writetags;
                //$tagwriter->filename = '/path/to/file.mp3';
                $tagwriter->filename = $filename;
                //$tagwriter->tagformats = array('id3v1', 'id3v2.3');
                $tagwriter->tagformats = array('id3v2.3');

                // set various options (optional)
                $tagwriter->overwrite_tags = true;
                $tagwriter->tag_encoding = $TextEncoding;
                $tagwriter->remove_other_tags = true;

                $artist = !empty($artist) ? $artist : 'unknown';
                $album = !empty($album) ? $album : 'unknown';

                // populate data array
                $TagData = array(
                    'title'         => array($title),
                    'artist'        => array($artist),
                    'album'         => array($album),
                    'year'          => array(date('Y', strtotime($track->album->release_date))),
                    'comment'		=> array($comment),
                );

                if ($fd = fopen($cover, 'rb')) {
                    $APICdata = fread($fd, filesize($cover));
                    fclose ($fd);
                    list($APIC_width, $APIC_height, $APIC_imageTypeID) = GetImageSize($cover);
                    $imagetypes = array(1=>'gif', 2=>'jpeg', 3=>'png');
                    if (isset($imagetypes[$APIC_imageTypeID])) {

                        $TagData['attached_picture'][0]['data']          = $APICdata;
                        $TagData['attached_picture'][0]['picturetypeid'] = '0x03';
                        $TagData['attached_picture'][0]['description']   = $title;
                        $TagData['attached_picture'][0]['mime']          = 'image/'.$imagetypes[$APIC_imageTypeID];

                    }
                }
                $tagwriter->tag_data = $TagData;
                $tagwriter->WriteTags();
            }
        }
    }

    public function beforeDownload($slug){
        try{
           // dd(["this",$slug]);
            if(!empty($slug))
            {
                $track = Track::with('album', 'artist')->where('slug', $slug)->first();

                if($track) {
                    $filename = 'uploads/audios/'.$track->location.'/'.$track->slug.'.mp3';
                    //$filename = 'http://localhost:8080/public/uploads/audios/'.$track->location.'/'.$track->slug.'.mp3';

                    $trackSize = Track::where('slug', $slug)->get(['filesize']);

                    $fileSize = round($trackSize[0]->filesize/1048576);

                    $user = Auth::user();

                    if( $user ) {
                        //echo $user->is_subscribed;
                        //dd($user->subscriber);

                        if( count($user->subscriber) != 0 ) {

                            $timeLimit = strtotime($user->subscriber->time_limit);

                            $currentTime = strtotime(date('j-m-Y', time()));

                            $downloadsLeft = $user->subscriber->download_limit - $user->subscriber->downloaded_data;

                            if( $timeLimit >= $currentTime && $downloadsLeft >= $fileSize) {
                                //dd([$timeLimit, $currentTime, $downloadsLeft]);
                                //$this->download($slug);

                                return json_encode(array('message' => 'Access denied.', 'status' => 'success'));

                            } else {

                                return json_encode(array('message' => 'You need to upgrade your subscription', 'status' => 'error'));
                            }

                        }else{

                            return json_encode(array('message' => "You need to subscribe first."));
                        }

                    }else{

                        return json_encode(array('message' => 'You need to login.', 'status' => 'error'));
                    }
                }
            }
        }catch(Exception $ex){
            console.log($ex);
        }

    }

    public function download($slug)
    {
        //$slug = Input::get('slug');

        if(!empty($slug))
        {
            $track = Track::with('album', 'artist')->where('slug', $slug)->first();


            if($track) {
                $filename = 'uploads/audios/'.$track->location.'/org/'.$track->slug.'.mp3';
                //$filename = 'uploads/audios/'.$track->location.'/'.$track->slug.'.mp3';

                //$trackSize = Track::where('slug', $slug)->get(['filesize']);

                //$fileSize = round($trackSize[0]->filesize/1000000);
                //$fileSize =round($trackSize[0]->filesize/1000000);

                $user = Auth::user();

                if( $user ) {
                    //echo $user->is_subscribed;
                    //dd(filesize($filename));
                    //dd(count($user->subscriber));

                    //dd($user->subscriber);

                    if( count($user->subscriber) != 0 ) {

                        $timeLimit = strtotime($user->subscriber->time_limit);

                        $currentTime = strtotime(date('j-m-Y', time()));

                        $downloadsLeft = $user->subscriber->download_limit - $user->subscriber->downloaded_data;

                        $fileSize = round(filesize($filename) / 1048576);

                        if( $timeLimit >= $currentTime && $downloadsLeft >= $fileSize) {


                            $title = $track->artist->name.' - '.$track->title . ' ' . Setting::first()->website_name;
                            //dd([$title,$filename,$slug]);
                            if (file_exists($filename)) {

                                $size = filesize($filename);
                                header('Content-Description: File Transfer');
                                header('Content-Type: application/octet-stream');
                                header('Content-Disposition: attachment; filename="' . basename($title) . '.mp3"');
                                header('Content-Transfer-Encoding: binary');
                                header('Connection: Keep-Alive');
                                header('Expires: 0');
                                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                                header('Pragma: public');
                                header('Content-Length: ' . $size);
                                readfile($filename);
                            }
                            $track->increment('downloads');
                            $track->save();

                            //Adding downloading data to user downloaded data
                            $userId = Auth::user()->id;

                            $user = User::find($userId)->subscriber;
                            $user->downloaded_data = $user->downloaded_data + $fileSize;

                            $user->save();
                        } else {

                            return json_encode(array('message' => 'You need to upgrade your subscription.', 'status' => 'error'));
                        }

                    }else{

                        return json_encode(array('message' => "You need to subscribe first."));
                    }

                }else{

                    return json_encode(array('message' => 'You need to login.', 'status' => 'error'));
                }
            }
        }
    }
}