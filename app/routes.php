<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::when('*', 'csrf', array('post'));

Route::get('/', 'HomeController@index');
Route::post('install', 'HomeController@install');


//Route::get('/', function(){
    //Artisan::call('db:seed', array('--quiet' => true, '--force' => true, '--class' => 'PagesTableSeeder'));
//});

Route::get('album', 'AlbumController@indexPublic');
Route::get('browse/{filter?}', 'AlbumController@indexPublic');
Route::get('album/{slug}', 'AlbumController@show');
Route::post('album/{id}/play', 'AlbumController@albumPlay');

Route::get('track/{slug}', 'TrackController@showTrack');

//Route::get('artist', 'ArtistController@indexPublic');
//Route::get('artist/{name}', 'ArtistController@show');         //Artist Route is commented.

Route::get('category', 'CategoryController@indexPublic');
Route::get('category/{name}', 'CategoryController@show');

Route::get('language', 'LanguageController@indexPublic');
Route::get('language/{name}', 'LanguageController@show');


Route::get('download/track/before/{slug}', 'TrackController@beforeDownload');     // Check Tracks Before downloading
Route::get('download/track/{slug}', 'TrackController@download');
Route::get('download/album/before/{slug}', 'AlbumController@beforeDownload');     // Check Albums Before downloading
Route::get('download/album/{slug}', 'AlbumController@download');

Route::get('ads/{type}', function($type){
    return View::make('ads.index', array('type' => $type));
});

Route::group(
    array('before' => 'auth'),
    function () {
        Route::post('album/favorite', 'AlbumLikeController@create');
        Route::post('track/favorite', 'TrackLikeController@create');
        Route::get('favorites', 'TrackController@index');
        Route::get('favorites/albums', 'AlbumController@likedAlbums');
        Route::get('playlist', 'PlaylistController@index');
        Route::post('playlist', 'PlaylistController@create');
        Route::post('playlist/track/add', 'PlaylistController@addTrack');
        Route::post('playlist/album/add', 'PlaylistController@addAlbum');
        Route::get('playlist/{id}', 'PlaylistController@show');
        Route::post('playlist/{id}/play', 'PlaylistController@playlistPlay');
        Route::post('playlist/{id}/edit', 'PlaylistController@edit');
        Route::post('playlist/{id}/track/remove', 'PlaylistController@removeTrack');
        Route::post('playlist/{id}/delete', 'PlaylistController@delete');
        Route::get('settings', 'UserController@settings');
        Route::post('settings', 'UserController@update');
    }
);

Route::group(
    array('before' => 'admin'),
    function () {
        Route::get('admin', 'AdminController@index');

        Route::get('admin/album', 'AdminController@index');

        Route::get('admin/bulkupload', 'TrackController@uploadBulk');
        Route::post('admin/startbulkupload', 'TrackController@startUploadBulk');

		Route::get('admin/album/create', 'AlbumController@create');
        Route::post('admin/album/create', 'AlbumController@store');

		Route::get('admin/album/{id}/add', 'TrackController@create');
        Route::post('admin/album/{id}/add', 'TrackController@store');

        Route::post('admin/album/edit', 'AlbumController@update');


        Route::get('admin/album/{id}/featured', 'AlbumController@featured');

        Route::get('admin/album/{id}/delete', 'AlbumController@delete');
        Route::get('admin/track/{id}/delete', 'TrackController@delete');

        Route::post('admin/track/edit', 'TrackController@update');

        Route::get('admin/artist', 'ArtistController@index');
        Route::post('admin/artist', 'ArtistController@update');
        Route::get('admin/artist/{slug}', 'ArtistController@showAdmin');

        Route::get('admin/settings', 'AdminController@getSettings');
        Route::post('admin/settings', 'AdminController@updateSettings');

        Route::get('admin/user', 'AdminController@getUsers');

        Route::post('admin/user/edit', 'UserController@update');
        Route::post('admin/user/mode', 'AdminController@toggleMode');
        Route::post('admin/user/status', 'AdminController@toggleStatus');

        Route::get('admin/pages', 'AdminController@getPages');
        Route::post('admin/pages', 'AdminController@updatePages');

        Route::get('admin/categories', 'CategoryController@index');
        Route::post('admin/category/{id}', 'CategoryController@update');
        Route::get('admin/category/{slug}', 'CategoryController@showAdmin');

        Route::get('admin/languages', 'LanguageController@index');
        Route::post('admin/language/{id}', 'LanguageController@update');
        Route::get('admin/language/{slug}', 'LanguageController@showAdmin');

    }
);

Route::get(
    'admin/api/artists/{name}.json',
    function ($name) {
        $name = htmlspecialchars($name);
        if (!empty($name)) {
            return DB::table('artists')->where('name', 'like', '%'.$name.'%')->take(5)->get(array('name', 'slug'));

        }
    }
);

Route::get(
    'admin/api/categories/{name}.json',
    function ($name) {
        $name = htmlspecialchars($name);
        if (!empty($name)) {
            return DB::table('categories')->where('name', 'like', '%'.$name.'%')->take(5)->get(array('name'));

        }
    }
);

Route::get(
    'admin/api/languages/{name}.json',
    function ($name) {
        $name = htmlspecialchars($name);
        if (!empty($name)) {
            return DB::table('languages')->where('name', 'like', '%'.$name.'%')->take(5)->get(array('name'));

        }
    }
);

Route::get(
    'admin/api/search/{name}.json',
    function ($name) {
        $name = htmlspecialchars($name);
        if (!empty($name)) {
            return DB::table('tracks')->where('title', 'like', '%'.$name.'%')->take(5)->get(array('title as name', 'slug'));
        }
    }
);

Route::get(
    'admin/api/albums/{name}.json',
    function ($name) {
        $name = htmlspecialchars($name);
        if (!empty($name)) {
            return DB::table('albums')->where('title', 'like', '%'.$name.'%')->take(5)->get(array('title as name', 'slug'));
        }
    }
);

// User Controller Routes
Route::post('login', 'UserController@login');
Route::post('signup', 'UserController@signup');

Route::post('password/remind', 'RemindersController@postRemind');
// ********* Activation **** //
Route::get('activation', 'UserController@activation');

Route::get('password/reset/{token}', 'RemindersController@getReset');
Route::post('password/reset/{token}', 'RemindersController@postReset');

// ********* PAGES ******** //
Route::get('page/{title}', 'PageController@show');


Route::get(
    'logout',
    function () {
        Auth::logout();
        return Redirect::to('/');
    }
);

Route::get('auth/facebook', 'UserController@loginWithFacebook');
Route::get('auth/twitter', 'UserController@loginWithTwitter');
Route::get('auth/google', 'UserController@loginWithGoogle');

Route::get('update', 'HomeController@update');

Event::listen('illuminate.query', function($sql)
{
    //var_dump($sql);
});

/*****  User Subscriptions *****/


Route::post('admin/user/subscriber', 'SubscriberController@index');

Route::post('adminsignup', 'UserController@signup');