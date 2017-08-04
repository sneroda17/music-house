<?php

class CategoryController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /pages
	 *
	 * @return Response
	 */
	public function index()
	{
		$categories = Category::paginate(30);
		return View::make('admin.categories', array('categories' => $categories));
	}

	public function indexPublic()
	{
		$categories = Category::paginate(30);
		return View::make('category.index', array('categories' => $categories));
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

    	$category = Category::with("genres")->where('slug', $name)->first();
    	if($category)
    	{
        	$ptitle = 'All '. $category->name . ' Albums';
        	return View::make('album.list', array('data' => $category, 'ptitle' => $ptitle));
        }
    }

    public function showAdmin($slug)
    {
    	$category = Category::where('slug', $slug)->first();
    	if($category)
    	{
        	$ptitle = 'All '. $category->name . ' Albums';
        	return View::make('admin.album.search', array('data' => $category, 'ptitle' => $ptitle));
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

        	$category = Category::where('id', $id)->first();
        	$category->name = $input['name'];
        	$category->slug = Custom::slugify($input['name']);
        	$category->save();

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