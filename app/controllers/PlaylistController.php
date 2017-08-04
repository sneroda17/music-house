<?php

class PlaylistController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /playlist
	 *
	 * @return Response
	 */
	public function index()
	{
		$playlists = Playlist::where('user_id', Auth::user()->id)->paginate(29);
	    return View::make('playlist.index', array('playlists' => $playlists));
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /playlist/create
	 *
	 * @return Response
	 */
	public function create()
	{
		if(!Auth::user()->active)
		{
			return json_encode(array('message' => Lang::get('words.register-success'), 'status' => 'error'));
		}
		$input = Input::all();

		$validator = Validator::make(
            $input,
            array(
                'title' => 'required|between:3,100',
            )
        );

        if ($validator->passes()) {
        	$slug = Custom::slugify('', 10);
            while (Playlist::where('slug', $slug)->where('user_id', Auth::user()->id)->first()) {
                $slug = Custom::slugify('', 10);
            }
            $playlist = new Playlist();
            $playlist->slug = $slug;
            $playlist->title = htmlspecialchars($input['title']);
            $playlist->user_id = Auth::user()->id;
            $playlist->save();
            return json_encode(array('status' => 'success', 'message' => Lang::get('words.playlist-created')));
        } else {
            $message = $validator->messages()->all()[0];
            return json_encode(array('message' => $message, 'status' => 'error'));
        }
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /playlist
	 *
	 * @return Response
	 */
	public function addTrack()
	{
		if(!Auth::user()->active)
		{
			return json_encode(array('message' => Lang::get('words.register-success'), 'status' => 'error'));
		}
		$input = Input::all();

		$validator = Validator::make(
            $input,
            array(
                'track' => 'required',
                'playlist' => 'required',
            )
        );

        if ($validator->passes()) {
        	$pt = PlaylistTrack::where('playlist_id', $input['playlist'])->where('track_id', $input['track'])->first();
        	if(!$pt) {
        		$pt_new = new PlaylistTrack();
        		$pt_new->track_id = $input['track'];
        		$pt_new->playlist_id = $input['playlist'];
        		$pt_new->save();
        	}
            return json_encode(array('status' => 'success', 'message' => Lang::get('words.track-added')));
        } else {
            $message = $validator->messages()->all()[0];
            return json_encode(array('message' => $message, 'status' => 'error'));
        }
	}

	/**
	 * Display the specified resource.
	 * GET /playlist/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($slug)
    {
    	$playlist = Playlist::where('slug', $slug)->first();
    	//dd($playlist->toArray());
    	if($playlist) {
        	return View::make('playlist.show', array('playlist' => $playlist));
        }
    }

    public function playlistPlay($slug)
    {
    	$playlist = Playlist::with('tracks', 'tracks.artist')->where('slug', $slug)->first();
    	//dd($playlist->toArray());
    	$dump = '';
    	if($playlist) {    		
        	$tracks = $playlist->tracks;
            if($tracks->count()) {
                foreach($tracks as $track) {
                    $dump .= '<li><a href="'.URL::to("uploads/audios/".$track->location."/".$track->slug.".mp3").'"><span>'.$track->title.'</span> - <i>'.$track->artist->name.'</i></a></li>';
                }
            }
        }
        return $dump;
    }

	/**
	 * Show the form for editing the specified resource.
	 * GET /playlist/{id}/edit
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$playlist = Playlist::where('slug', $id)->first();
    	//dd($playlist->toArray());
    	if($playlist) {
        	$input = Input::all();
			$validator = Validator::make(
	            $input,
	            array(
	                'title' => 'required|between:3,100',
	            )
	        );

	        if ($validator->passes()) {
	        	
	            $playlist->title = htmlspecialchars($input['title']);
	            $playlist->save();
	            return json_encode(array('status' => 'success', 'slug' => $playlist->slug, 'message' => 'Edit success'));
	        } else {
	            $message = $validator->messages()->all()[0];
	            return json_encode(array('message' => $message, 'status' => 'error'));
	        }
        }
	}

	public function removeTrack($id)
	{

        	$input = Input::all();
			$validator = Validator::make(
	            $input,
	            array(
	                'track' => 'required',
	            )
	        );

	        if ($validator->passes()) {
	        	$pl_track = PlaylistTrack::where('playlist_id', $id)->where('track_id', $input['track'])->first();
	        	if($pl_track) {
	        		$pl_track->delete();
	            	return json_encode(array('status' => 'success', 'slug' => $input["playlist"], 'message' => 'track removed'));
	            }
	        } else {
	            $message = $validator->messages()->all()[0];
	            return json_encode(array('message' => $message, 'status' => 'error'));
	        }
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /playlist/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /playlist/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function delete($id)
	{
		$playlist = Playlist::where('slug', $id)->where('user_id', Auth::user()->id)->first();
    	//dd($playlist->toArray());
    	if($playlist) {
    		$pl_tracks = PlaylistTrack::where('playlist_id', $playlist->id);
    		if($pl_tracks) {
    			$pl_tracks->delete();
    		}
    		$playlist->delete();
    		return json_encode(array('status' => 'success', 'message' => Lang::get('words.playlist-deleted')));
    	}
	}

}