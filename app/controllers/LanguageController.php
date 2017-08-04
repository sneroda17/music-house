<?php

class LanguageController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /pages
	 *
	 * @return Response
	 */
	public function index()
	{
		$languages = Language::paginate(30);
		return View::make('admin.languages', array('languages' => $languages));
	}

	public function indexPublic()
	{
		$languages = Language::paginate(30);
		return View::make('language.index', array('languages' => $languages));
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
    	$language = Language::where('slug', $name)->first();
    	if($language)
    	{
    		$ptitle = 'All '. $language->name . ' Albums';
        	return View::make('album.list', array('data' => $language, 'ptitle' => $ptitle));
        }
    }

    public function showAdmin($slug)
    {
    	$language = Language::where('slug', $slug)->first();
    	if($language)
    	{
        	$ptitle = 'All '. $language->name . ' Albums';
        	return View::make('admin.album.search', array('data' => $language, 'ptitle' => $ptitle));
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
	public function update($id)
	{
		$input = Input::all();
        $validator = Validator::make(
            $input,
            array(
                'name' => 'required|between:2,100',
            )
        );

        if ($validator->passes()) {

        	$language = Language::where('id', $id)->first();
        	$language->name = $input['name'];
        	$language->slug = Custom::slugify($input['name']);
        	$language->save();

        	return json_encode(array('message' => 'successfull', 'status' => 'success'));

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