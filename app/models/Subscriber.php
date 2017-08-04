<?php

class Subscriber extends \Eloquent {
    protected $fillable = [];
    protected $table = "subscribers";

    public function user(){

        return $this->belongsTo('User');

    }
}