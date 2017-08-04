<?php

class AlbumLike extends \Eloquent {
	protected $fillable = [];

	protected $table = "album_likes";

	public function album()
	{
		return $this->belongsTo('Album');
	}
}