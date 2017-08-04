<?php

class Album extends \Eloquent {
	
	protected $fillable = [];
	protected $table = 'albums';
	public $timestamps = true;


	public function user()
	{
		return $this->belongsTo('User');
	}

	public function likes()
	{
		return $this->hasMany('AlbumLike')->count();
	}

	public function is_favorite()
	{
		$user_id = Auth::guest() ? 0 : Auth::user()->id;
		return $this->hasMany('AlbumLike')->where('user_id', $user_id);
	}

	public function tracks()
	{
		return $this->hasMany('Track');
	}
	public function tracksCount()
	{
		return $this->hasMany('Track')->count();
	}

	public function artist()
	{
		return $this->belongsTo('Artist')->select(array('id', 'name', 'slug'));
	}
    public function publisher()
    {
        return $this->belongsTo('Publisher')->select(array('id', 'name', 'slug'));
    }

	public function language()
	{
		return $this->belongsTo('Language')->select(array('id', 'name', 'slug'));
	}

	/*public function category()
	{
		return $this->belongsTo('Category')->select(array('id', 'name', 'slug'));
	}*/
    public function categories()
    {
        return $this->hasMany('AlbumsGenre')->leftJoin("categories","albumsgenre.category_id","=","categories.id");
        //return $this->hasMany('AlbumsGenre')->join("categories","albumsgenre.category_id","=","categories.id", 'left outer');

    }

	public function albumDuration()
	{
		return DB::table('tracks')->where('album_id', $this->id)->sum('duration');
	}

	public function getPopularity($tracks)
	{
		return DB::table('track_likes')->whereIn('track_id', $tracks)->select(DB::raw('track_id, count(id) AS count'))->groupBy('track_id');
	}

}