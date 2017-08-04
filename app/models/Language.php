<?php

class Language extends \Eloquent {
	protected $fillable = [];
	public $timestamps = false;
	protected $table = "languages";

	public function albums()
	{
		return $this->hasMany('Album');
	}
}