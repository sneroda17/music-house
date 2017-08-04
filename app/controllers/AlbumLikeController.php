<?php

class AlbumLikeController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /albumlike
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /albumlike/create
	 *
	 * @return Response
	 */
	public function create()
	{
		if(!Auth::guest())
		{
			if(!Auth::user()->active)
			{
				return json_encode(array('message' => Lang::get('words.register-success'), 'status' => 'error'));
			}
			
			$input = Input::all();
			$user_id = Auth::user()->id;
			$album_id = $input['album_id'];

			$albumlike = AlbumLike::where('user_id', $user_id)->where('album_id', $album_id)->first();

			if($albumlike)
			{
				$albumlike->delete();
			}
			else
			{
				$al = new AlbumLike();
				$al->user_id = $user_id;
				$al->album_id = $album_id;
				$al->save();

			}
			$count = AlbumLike::where('album_id', $album_id)->count();
			return json_encode(array('status' => 'success', 'count' => $count));
		}
		else
		{
			return json_encode(array('status' => 'error', 'message' => Lang::get('words.auth-failed')));
		}
	}

	/**
	 * Store a newly created resource in storage.
	 * album /albumlike
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /albumlike/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /albumlike/{id}/edit
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /albumlike/{id}
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
	 * DELETE /albumlike/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}