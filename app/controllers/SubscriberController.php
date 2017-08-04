<?php


class SubscriberController extends \BaseController {

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    public function index($id = null)
    {
        //print_r($input);
        $input = Input::all();

        $validator = Validator::make(
            $input,
            array(
                'subscription'  => 'required',
                'expire_date'   => 'required',
                'expire_month'  => 'required',
                'expire_year'   => 'required',
                'download_limit'=> 'integer'
            )
        );

        //$user = User::find($id);

        if ($validator->passes()) {

            if($id == null){
                $id = Input::get('user');
            }

            $subscriber = Subscriber::where('user_id', $id)->first();

            //storing time in a variable

            if($subscriber) {

                $expire_date    = Input::get('expire_date');
                $expire_month   = Input::get('expire_month');
                $expire_year    = Input::get('expire_year');
                $time_limit     = $expire_date . '-' . $expire_month . '-' . $expire_year;

                $subscriber->user_id        = $id;
                $subscriber->is_subscribed  = Input::get('subscription');
                $subscriber->time_limit     = $time_limit;
                $subscriber->download_limit = Input::get('download_limit') + $subscriber->download_limit;

                $subscriber->save();
            }
            else{

                $subscriber = new Subscriber();

                $expire_date = Input::get('expire_date');
                $expire_month = Input::get('expire_month');
                $expire_year = Input::get('expire_year');
                $time_limit = $expire_date . '-' . $expire_month . '-' . $expire_year;

                $subscriber->user_id = $id;
                $subscriber->is_subscribed = Input::get('subscription');
                $subscriber->time_limit = $time_limit;
                $subscriber->download_limit = Input::get('download_limit');

                $subscriber->save();

            }


            return json_encode(array('message' => 'Subscription has updated.', 'status' => 'success'));

            //dd($input);
        }
        return json_encode(array('status' => 'error', 'message' => $validator->messages()->all()[0]));





    }

    public function subs(){

        //return User::find(1);

        return Subscriber::find(1);

        //$user->subscribe()->save($subs);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }


    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {



    }


    /**
     * Display the specified resource.
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
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }


}
