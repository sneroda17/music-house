<?php

class Publisher extends \Eloquent {
	protected $fillable = [];
    public $timestamps = false;
    protected $table = "publishers";

    public function albums()
    {
        return $this->hasMany('Album');
    }
}