<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';
	public $timestamps = true;
	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password', 'remember_token');

	public function albums(){
		return $this->hasMany('Album');
	}

	public function likedAlbums()
    {
        return $this->hasManyThrough('Album', 'AlbumLike', 'album_id', 'id');
    }

    public function likedTracks()
    {
        return $this->hasManyThrough('Track', 'TrackLike', 'track_id', 'id');
    }

    public function albumsLiked()
    {
    	return $this->hasMany('AlbumLike');
    }

    public function tracksLiked()
    {
    	return $this->hasMany('TrackLike');
    }

    public function playlistsCount()
    {
    	return DB::table('playlists')->where('user_id', $this->id)->count();
    }


    public function subscriber(){

        return $this->hasOne('Subscriber');
    }
}
