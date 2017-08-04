<?php

class Playlist extends \Eloquent {
	protected $fillable = [];
	protected $table = "playlists";

	public function user()
	{
		return $this->belongsTo('User');
	}

	public function tracks()
	{
		return $this->belongsToMany('Track', 'playlist_tracks', 'playlist_id', 'track_id');
	}

	public function tracksCount()
	{
		return $this->hasMany('PlaylistTrack')->count();
	}

	public function getPopularity($tracks)
	{
		return DB::table('track_likes')->whereIn('track_id', $tracks)->select(DB::raw('track_id, count(id) AS count'))->groupBy('track_id');
	}
	
}