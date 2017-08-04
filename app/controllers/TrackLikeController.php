<?php

class TrackLikeController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /tracklike
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /tracklike/create
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
			$track_id = $input['track_id'];

			$tracklike = TrackLike::where('user_id', $user_id)->where('track_id', $track_id)->first();

			if($tracklike)
			{
				$tracklike->delete();
			}
			else
			{
				$al = new TrackLike();
				$al->user_id = $user_id;
				$al->track_id = $track_id;
				$al->save();

			}
			$count = TrackLike::where('track_id', $track_id)->count();
			return json_encode(array('status' => 'success', 'count' => $count));
		}
		else
		{
			return json_encode(array('status' => 'error', 'message' => Lang::get('words.auth-failed')));
		}
	}

	/**
	 * Store a newly created resource in storage.
	 * track /tracklike
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /tracklike/{id}
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
	 * GET /tracklike/{id}/edit
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
	 * PUT /tracklike/{id}
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
	 * DELETE /tracklike/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}