<?php

class Artist extends \Eloquent {
	protected $fillable = [];
	public $timestamps = false;
	protected $table = "artists";

	public function albums()
	{
		return $this->hasMany('Album');
	}

	public function tracks()
	{
		return $this->hasMany('Track');
	}	

	public function albumsCount()
	{
		return $this->hasMany('Album')->count();
	}
}