<?php

class AdminController extends \BaseController {

    /**
     * Display a listing of the resource.
     * GET /admin
     *
     * @return Response
     */
    public function index()
    {
        try{

            $albums = Input::get('q') ? Album::with('artist', 'categories', 'language')->where('title', 'like', '%'.Input::get('q').'%')->orderBy('created_at', 'desc')->paginate(20) : Album::with('artist', 'categories', 'language')->orderBy('created_at', 'desc')->paginate(20);

            //print_r($albums);
            //dd(DB::getQueryLog());
            //die;
            return View::make('admin.album.index', array('albums' => $albums));
        }catch (Exception $ex){
            echo $ex->getMessage();
        }
        /*$files = PATH::asset('assets/mp3/01 - Aanewala Pal-(MyMp3Singer.net).mp3');

        echo $files;
        $getID3 = new \getID3;
        $info = $getID3->analyze($files);
        print_r($info);
        die;*/




        //return View::make('admin.album.index', array('albums' => $albums));
    }

    public function getUsers()
    {
        $users = Input::get('q') ? User::with("subscriber")->where('username', 'like', '%'.Input::get('q').'%')->paginate(20) :User::with("subscriber")->paginate(20);

        return View::make('admin.user', array('users' => $users));
    }

    public function editUser($id)
    {
        $user = User::with("subscriber")->where('id', $id)->first();
        if($user)
        {
            return View::make('admin.users.edit', array('user' => $user));
        }
    }

    public function toggleStatus()
    {
        $id = Input::get('user');
        if(Auth::user()->id != $id)
        {
            $user = User::where('id', $id)->first();
            $state = $user->banned ? 0 : 1;
            $user->banned = $state;
            $user->save();
            return json_encode(array('message' => Lang::get('words.user-status-changed'), 'status' => 'success'));
        }
        else {
            return json_encode(array('message' => Lang::get('words.cant-disable'), 'status' => 'error'));
        }
    }

    public function toggleMode()
    {
        $id = Input::get('user');
        if(Auth::user()->id != $id)
        {
            $user = User::where('id', $id)->first();
            $state = $user->admin ? 0 : 1;
            $user->admin = $state;
            $user->save();
            return json_encode(array('message' => Lang::get('words.user-level-changed'), 'status' => 'success'));
        }
        else {
            return json_encode(array('message' => Lang::get('words.cant-disable'), 'status' => 'error'));
        }
    }

    public function getPages()
    {

        $pages = Page::all();
        return View::make('admin.pages', array('pages' => $pages));
    }

    // ********** Pages update method ********** //

    public function updatePages()
    {
        $input = Input::all();
        $pages = Page::all();
        foreach ($pages as $page) {
            $page->description = $input[$page->title];
            $page->save();
        }

        return Redirect::to('admin/pages')->with(array('message' => Lang::get('words.pages-updated'), 'status' => 'showSuccessToast'));
    }

    public function getSettings()
    {
        $settings = Setting::first();
        return View::make('admin.settings', array('settings' => $settings));
    }

    public function updateSettings()
    {
        $inputs = Input::all();
        $settings = Setting::first();
        $settings->website_name = htmlspecialchars($inputs['website_name']);
        $settings->website_title = htmlspecialchars($inputs['website_title']);
        $settings->website_description = htmlspecialchars($inputs['website_description']);
        $settings->recaptcha_site_key = htmlspecialchars($inputs['recaptcha_site_key']);
        $settings->recaptcha_secret_key = htmlspecialchars($inputs['recaptcha_secret_key']);
        $settings->analytics = htmlspecialchars($inputs['analytics']);

        $settings->fb_page_id = htmlspecialchars($inputs['fb_page_id']);
        $settings->fb_key = htmlspecialchars($inputs['fb_key']);
        $settings->fb_secret_key = htmlspecialchars($inputs['fb_secret_key']);
        $settings->twitter_page_id = htmlspecialchars($inputs['twitter_page_id']);
        $settings->twitter_key = htmlspecialchars($inputs['twitter_key']);
        $settings->twitter_secret_key = htmlspecialchars($inputs['twitter_secret_key']);
        $settings->google_page_id = htmlspecialchars($inputs['google_page_id']);
        $settings->google_key = htmlspecialchars($inputs['google_key']);
        $settings->google_secret_key = htmlspecialchars($inputs['google_secret_key']);

        $settings->theme_color = htmlspecialchars($inputs['theme_color']);

        $settings->auth_download = isset($inputs['auth_download']) ? 1 : 0;
        $settings->zip_download = isset($inputs['zip_download']) ? 1 : 0;
        $settings->downloadable = isset($inputs['downloadable']) ? 1 : 0;

        $settings->box_ad = htmlspecialchars($inputs['box_ad']);
        $settings->banner_ad = htmlspecialchars($inputs['banner_ad']);

        $settings->youtube_key = htmlspecialchars($inputs['youtube_key']);

        $message = Lang::get('words.settings-updated');
        $status = 'success';

        if(isset($inputs['logo_image']))
        {
            $is_valid = Validator::make($inputs, array('logo_image' => 'mimes:jpeg,bmp,png'));
            if ($is_valid->passes()) {
                Custom::imgUpload(Input::file('logo_image'), 'logo', 'assets', false, false);
            } else {
                $messages = $is_valid->messages()->all()[0];
                $status = 'error';
            }
        }

        $settings->save();
        return Redirect::to('/admin/settings')->with(array('message' => $message, 'status' => $status));
    }

    /**
     * Show the form for creating a new resource.
     * GET /admin/create
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     * POST /admin
     *
     * @return Response
     */
    public function store()
    {
        //
    }

    /**
     * Display the specified resource.
     * GET /admin/{id}
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     * GET /admin/{id}/edit
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
     * PUT /admin/{id}
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * DELETE /admin/{id}
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

}