<?php

class ArtistController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /pages
	 *
	 * @return Response
	 */
	public function index()
	{
		$artists = Input::get('q') ? Artist::where('name', 'like', '%'.Input::get('q').'%')->orderBy('id', 'desc')->paginate(30) : Artist::orderBy('id', 'desc')->paginate(30);
		return View::make('admin.artist.index', array('artists' => $artists));
	}

	public function indexPublic()
	{
		$q = htmlspecialchars(Input::get('q'));
        $q = trim($q);
        if(!empty($q)) {
            $artists = Artist::where('name', 'like', '%'.$q.'%')->paginate(24);
            return View::make('search.artist', array('artists' => $artists, 'searchKey' => $q));
        }
		$artists = Artist::orderBy('id', 'desc')->paginate(30);
		if($artists) {
			return View::make('artist.index', array('artists' => $artists));
		}
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /pages/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /pages
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /pages/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($name)
    {
    	$artist = Artist::where('slug', $name)->first();
    	if($artist)
    	{
        	$ptitle = 'All '. $artist->name . ' Albums';
        	return View::make('album.list', array('data' => $artist, 'ptitle' => $ptitle));
        }
    }

    public function showAdmin($slug)
    {
    	$artist = Artist::where('slug', $slug)->first();
    	if($artist)
    	{
        	$ptitle = 'All '. $artist->name . ' Albums';
        	return View::make('admin.album.search', array('data' => $artist, 'ptitle' => $ptitle));
        }
    }

	/**
	 * Show the form for editing the specified resource.
	 * GET /pages/{id}/edit
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
	 * PUT /pages/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update()
	{
		$input = Input::all();
        //dd($input);
        $validator = Validator::make(
            $input,
            array(
                'title' => 'required|between:2,100',
                'image' => 'mimes:jpeg,png',
                'imgurl' => 'url',
                'id' =>	'required',
            )
        );

        if ($validator->passes()) {

        	$artist = Artist::where('id', $input['id'])->first();

        	if($artist) {
        		$artist->name = htmlspecialchars(stripslashes($input['title']));
        		$artist->slug = Custom::slugify($artist->name);
        		$imgFile = Input::file('image');
        		$imgurl = Input::get('imgurl');
        		if (!empty($imgurl)) {
	                $artist->image = Custom::imgUpload($imgurl, $artist->slug, 'artists', false, true);
	            } elseif(!empty($imgFile)) {
        			$artist->image = Custom::imgUpload($imgFile, $artist->slug, 'artists', false, false);
        		}
        		$artist->save();
        		return json_encode(array('message' => 'success edit', 'status' => 'success'));
        	}

        } else {
            $message = $validator->messages()->all()[0];
            return json_encode(array('message' => $message, 'status' => 'error'));
        }
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /pages/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}