<?php

class HomeController extends BaseController {

    /*
    |--------------------------------------------------------------------------
    | Default Home Controller
    |--------------------------------------------------------------------------
    |
    | You may wish to use controllers instead of, or in addition to, Closure
    | based routes. That's great! Here is an example controller method to
    | get you started. To route to this controller, just add the route:
    |
    |	Route::get('/', 'HomeController@showWelcome');
    |
    */

    public function index()
    {
    	try {
            $settings = Setting::first();

            if (!$settings) {
                throw new Exception("Install Script");
            }
            $searchKey = htmlspecialchars(Input::get('q'));
            if (!empty($searchKey)) {
                $searchKey = trim($searchKey);
                $tracks = Track::with('album', 'artist', 'is_favorite')->where('title', 'like', '%'.$searchKey.'%')->paginate(20);
                //dd($tracks->toArray());
                return View::make('search.index', array('tracks' => $tracks, 'searchKey' => $searchKey));
            } else {
                $albums = Album::with('artist')->orderBy('id', 'desc')->paginate(30);
                $topAlbums = Album::with('artist')->leftJoin('album_likes', 'albums.id', '=', 'album_likes.album_id')
                                    ->where('album_likes.created_at', '>=', date('Y-m-d H:i:s', strtotime('-1 month')))
                                    ->groupBy('album_likes.album_id')
                                    ->orderBy(DB::raw('COUNT(album_likes.id)'), 'DESC')
                                    ->orderBy('created_at', 'DESC')
                                    ->select('albums.*')
                                    ->take(20)
                                    ->get();

                $trendingAlbums = Album::with('artist')
                                            ->where('created_at', '>=', date('Y-m-d H:i:s', strtotime('-1 month')))
                                            ->orderBy('views', 'desc')->take(20)->get();
                $popularAlbums = Album::with('artist')
                                            ->where('created_at', '>=', date('Y-m-d H:i:s', strtotime('-1 month')))
                                            ->orderBy('downloads', 'desc')->take(10)->get();
                $featuredAlbums = Album::with('artist')
                    ->where('featured', 1)
                    ->orderBy('id', 'desc')->take(20)->get();
                $artists = Artist::orderBy('id', 'desc')->take(20)->get();

                 $tracks = Track::get();
                 //dd($tracks[0]->slug);
                return View::make('home.index', array('albums' => $albums, 'topAlbums' => $topAlbums, 'trendingAlbums' => $trendingAlbums, 'popularAlbums' => $popularAlbums, 'artists' => $artists,"featuredAlbums"=>$featuredAlbums, 'tracks'=> $tracks ));
            }                            
        }
        catch (Exception $ex) {
            return View::make('install');
        }
	}

	public function install() {
		$inputs = Input::all();
		$validator = Validator::make($inputs, array(
			'username' => 'required|alpha_dash|between:3,15',
			'password' => 'required|alpha_dash|between:3,15',
			'email' => 'required|email',
            'inputDBhost' => 'required',
            'inputDBname' => 'required',
            'inputDBusername' => 'required',
            )
        );

        if($validator->passes()) {
        	//dd('ok');
        	$con = @mysqli_connect($inputs['inputDBhost'], $inputs['inputDBusername'], $inputs['inputDBpassword'], $inputs['inputDBname']);
        	if(!mysqli_connect_errno($con)) {
        		$file = '../app/config/config.php';
                $configs = "<?php
                                return array(
                                        'DBhost' => '{$inputs['inputDBhost']}',
                                        'DBname' => '{$inputs['inputDBname']}',
                                        'DBuser' => '{$inputs['inputDBusername']}',
                                        'DBpassword' => '{$inputs['inputDBpassword']}'
                                    );";
                $write_config_file = file_put_contents($file, $configs);
                if ($write_config_file !== false) {
                	// Temporarily attempt to connect to database
                    Config::set('database.connections.mysql.host', $inputs['inputDBhost']);
                    Config::set('database.connections.mysql.database', $inputs['inputDBname']);
                    Config::set('database.connections.mysql.username', $inputs['inputDBusername']);
                    Config::set('database.connections.mysql.password', $inputs['inputDBpassword']);

                    //Migrate the database tables
                    Artisan::call('migrate', ['--quiet' => true, '--force' => true]);
                    
                    //Seed database tables with default settings.
                    Artisan::call('db:seed', array('--quiet' => true, '--force' => true, '--class' => 'SettingsTableSeeder'));
                    Artisan::call('db:seed', array('--quiet' => true, '--force' => true, '--class' => 'PagesTableSeeder'));
                    //Artisan::call('db:seed', array('--quiet' => true, '--force' => true, '--class' => 'PostsTableSeeder'));

                    $user = new User();
			        $user->username = $inputs['username'];
			        $user->email = $inputs['email'];
			        $user->password = Hash::make($inputs['password']);
			        $user->active = 1;
			        $user->admin = 1;
			        $user->save();

                    Auth::login($user, true);
                    return Redirect::to('/');

                }
                else {
                	return Redirect::to('/')->with(array('message' => 'couldn\'t write file'));
                }
        	}
        	else {
        		return Redirect::to('/')->with(array('message' => 'MySQL connection error'));
        	}
        }
        else {
        	return Redirect::to('/')->with(array('message' => 'Validation failed'.$validator->messages()->all()[0]));
        }
	}

    public function update() {
        $this->updateV1_3();
        return Redirect::to('/')->with(array('message' => 'Script updated Successfully!', 'status' => 'success'));
    }

    private function updateV1_2() {
        if(Schema::hasTable('settings')) {
            Schema::table('settings', function($table)
            {
                if (!Schema::hasColumn('settings', 'youtube_key'))
                {
                    $table->string('youtube_key');
                }
            });
        }
        return true;
    }

    private function updateV1_3() {
        if(Schema::hasTable('settings')) {
            Schema::table('settings', function($table)
            {
                if (!Schema::hasColumn('settings', 'banner_ad'))
                {
                    $table->string('banner_ad');
                }
            });
        }
        return true;
    }

}
