<?php

class Track extends \Eloquent {
	protected $fillable = [];
	protected $table = "tracks";

	public function album()
	{
		return $this->belongsTo('Album');
	}

	public function artist()
	{
		return $this->belongsTo('Artist');
	}
    public function category()
    {
        return $this->belongsTo('Category')->select(array('id', 'name', 'slug'));
    }

	public function likes()
	{
		return $this->hasMany('TrackLike')->count();
	}

	public function is_favorite()
	{
		$user_id = Auth::guest() ? 0 : Auth::user()->id;
		return $this->hasMany('TrackLike')->where('user_id', $user_id);
	}

	public function playlistTrack()
	{
		return $this->belongsTo('PlaylistTrack');
	}

}