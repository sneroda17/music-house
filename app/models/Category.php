<?php

class Category extends \Eloquent {
	protected $fillable = [];
	public $timestamps = false;
	protected $table = "categories";
    protected $guarded = array('id');

	/*public function albums()
	{
        //return $this->hasManyThrough('Album','AlbumGenre');
		return $this->hasMany('Album');
	}*/
    public function tracks()
    {
        return $this->hasMany('Track');
    }
    public function genres()
    {
        try{
            return $this->belongsToMany('Album',"albumsgenre","category_id","album_id");
        }catch (Exception $ex){
            echo $ex->getMessage();

        }

    }
}