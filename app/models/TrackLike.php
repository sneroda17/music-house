<?php

class TrackLike extends \Eloquent {
	protected $fillable = [];

	protected $table = "track_likes";
	public $timestamps = false;

	public function track()
	{
		return $this->belongsTo('Track');
	}

	public function getPopularity($tracks)
	{
		return DB::table('track_likes')->whereIn('track_id', $tracks)->select(DB::raw('track_id, count(id) AS count'))->groupBy('track_id');
	}
}