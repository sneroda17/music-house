<?php

class PlaylistTrack extends \Eloquent {
	protected $fillable = [];
	protected $table = "playlist_tracks";
	public $timestamps = false;
	
	public function playlist()
	{
		return $this->belongsTo('Playlist');
	}

	public function tracks()
	{
		return $this->hasMany('Track');
	}
}