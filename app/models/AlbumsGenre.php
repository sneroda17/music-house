<?php

class AlbumsGenre extends \Eloquent
{
    protected $fillable = ['album_id','category_id'];
    //protected $guarded = array('id');
    protected $table = 'albumsgenre';
    public $timestamps = false;

    public function albums()
    {

        return $this->hasMany('Album');
        //return $this->belongsTo('Category')->select(array('id', 'name', 'slug'));
    }
    public function categories(){
        return $this->hasManyThrough('Album','Category');
    }
    public function category(){
        return $this->belongsTo('Category')->select(array('id', 'name', 'slug'));
    }

}