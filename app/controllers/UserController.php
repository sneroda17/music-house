<?php
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class UserController extends \BaseController implements RemindableInterface {

    use RemindableTrait;

    //Rules required to register the user
    public static $adminrules = array(
        'username' => 'required|unique:users|alpha_dash|between:5,15',
        'email' => 'required|email|unique:users',
        'password' => 'required|alpha_dash|between:6,12|confirmed',
    );

    public static $rules = array(
        'username' => 'required|unique:users|alpha_dash|between:5,15',
        'email' => 'required|email|unique:users',
        'password' => 'required|alpha_dash|between:6,12|confirmed',
        //'g-recaptcha-response' => 'required',
    );

    /**
     * Show the form for creating a new resource.
     * GET /user/create
     *
     * @param  string $username
     * @param  string $email
     * @param  string $password
     * @param  boolean $active
     * @param  string $activation_token
     * @param  string $filename
     * @param  int $points
     * @return Response
     */
    public function create($username, $email, $password, $active = 0, $activation_token = null, $filename = null)
    {
        $user = new User();
        $user->username = $username;
        $user->email = $email;
        $user->password = $password;

        if(Auth::user() && Auth::user()->admin){

            $user->activation_token = null;
            $user->active = 1;

        }else{

            $user->active = $active;
        }

        //dd($username);

        $user->activation_token = $activation_token;
        if ($filename) {
            $user->avatar = $filename;
        }
        $user->save();

        return $user;
    }

    /**
     * Show the form for editing the specified resource.
     * GET /user/settings
     *
     * @return Response
     */
    public function settings()
    {
        $user = User::where('id', Auth::user()->id)->first();
        $data = array('user' => $user);
        return View::make('user', $data);
    }

    /**
     * Update the specified resource in storage.
     * PUT /user/{id}
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id = null)
    {
        $input = Input::all();
        $validator = Validator::make(
            $input,
            array(
                //'email' => 'required|email:unique:users',
                'password' => 'alpha_dash|between:6,12|confirmed|required',
                //'username' => 'alpha_dash|between:5,15',
            )
        );

        if ($validator->passes()) {
            if($id == null) {
                $id = $input['user'];
            }
            $user = User::find($id);
            $inputPassword = isset($input['password']) ? $input['password'] : null;
            if (!empty($inputPassword)) {
                $user->password = Hash::make($inputPassword);
            }
            $inputUsername = isset($input['username']) ? $input['username'] : null;
            if (!empty($inputUsername)) {
                $user->username = $inputUsername;
            }
            $inputEmail = isset($input['email']) ? $input['email'] : null;
            if (!empty($inputEmail) && $user->email != $inputEmail) {
                $user->email = $inputEmail;
                if(!$user->admin) {
                    $email = $inputEmail;
                    $user->active = 0;
                    $activation_token = str_random(64);
                    $user->activation_token = $activation_token;
                    $data = array('username' => $user->username, 'activation_token' => $activation_token);
                    Mail::send(
                        'emails.welcome',
                        $data,
                        function ($message) use ($email) {
                            $message->to($email)->subject(Lang::get('words.activation-subject'));
                        }
                    );
                }
            }
            $user->save();
            $redirectURL = $user->admin ? 'admin/user' : 'settings';
            return json_encode(array('status' => 'success', 'message' => Lang::get('words.password-updated'), 'url' => $redirectURL));
        }

        return json_encode(array('status' => 'error', 'message' => $validator->messages()->all()[0]));
    }

    public function sendActivation()
    {
        $user = Auth::user();
        if (!$user->active) {
            $email = $user->email;
            $activation_token = str_random(64);
            $user->activation_token = $activation_token;
            $data = array('username' => $user->username, 'activation_token' => $activation_token);
            Mail::send(
                'emails.welcome',
                $data,
                function ($message) use ($email) {
                    $message->to($email)->subject(Lang::get('words.activation-subject'));
                }
            );
            $user->save();
            return Redirect::to('/')->with(array('status' => 'showSuccessToast', 'message' => Lang::get('words.activation-resend')));
        } else {
            return Redirect::to('/')->with(array('status' => 'showErrorToast', 'message' => Lang::get('words.activation-resend')));
        }
    }

    public function upload($id, $type)
    {
        if (!Auth::guest()) {
            $input = Input::all();
            $validator = Validator::make(
                $input,
                array(
                    'image' => 'mimes:jpeg,png'
                )
            );
            if ($validator->passes()) {
                $user = User::find($id);
                $mediaDir = 'uploads/' . $type . 's/';

                $oldImgPath =  $mediaDir . $user->$type . '.jpg';

                $name = Custom::slugify('');

                $newImg = Custom::imgUpload($input['image'], $name, $type.'s', false, false);
                $newImg .= '/' . $name;
                $user->$type = $newImg;
                $user->save();
                if (file_exists($oldImgPath)) {
                    unlink($oldImgPath);
                }
                return json_encode(array('status' => 'success', 'message' => Lang::get('words.profile-updated'), 'img' => $newImg));
            }
            return json_encode(array('status' => 'error', 'message' => $validator->messages()->all()[0]));
        } else {
            return json_encode(array('status' => 'error', 'message' => Lang::get('words.auth-failed')));
        }
    }

    /**
     * Remove the specified resource from storage.
     * DELETE /user/{id}
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Login the user.
     * POST /login
     *
     * @return Response
     */
    public function login()
    {
        // get login POST data
        $email_login = array(
            'email' => Input::get('email'),
            'password' => Input::get('password'),
        );

        $username_login = array(
            'username' => Input::get('email'),
            'password' => Input::get('password'),
        );

        $remember = Input::get('remember') == 'on' ? true : false;

        if (Auth::attempt($email_login, $remember) || Auth::attempt($username_login, $remember)) {
            if (Auth::user()->banned) {
                Auth::logout();
                return json_encode(array('status' => 'error', 'message' => Lang::get('words.you-banned')));
            }
            return json_encode(array('status' => 'success', 'message' => Lang::get('words.login-success')));
        } else {
            return json_encode(array('status' => 'error', 'message' => Lang::get('words.login-error')));
        }

    }

    // *********** FACEBOOK OAUTH SIGNIN/SIGNUP ********** //

    public function loginWithFacebook()
    {
        $settings = Setting::first();
        $code = Input::get('code');

        $fb = OAuth::consumer('Facebook');

        if (!empty($code)) {
            // This was a callback request from facebook, get the token
            $token = $fb->requestAccessToken($code);

            // Send a request with it
            $result = json_decode($fb->request('/me'), true);
            $oauth_userid = $result['id'];
            $oauth_username = $result['name'];
            $oauth_username = Custom::slugify($oauth_username);
            $oauth_email = isset($result['email']) ? $result['email'] : $oauth_username.'@facebook.com';

            //dd($oauth_username);

            if (isset($oauth_userid) && isset($oauth_username)) {
                $fb_auth = OauthUser::where('oauth_uid', $oauth_userid)->where('service', 'facebook')->first();
                if (isset($fb_auth->id)) {
                    $user = User::find($fb_auth->user_id);
                } else {
                    // Execute Add or Login Oauth User
                    $user = User::where('email', $oauth_email)->first();
                    if (!isset($user->id)) {
                        $username = $this->createUsernameIfExists($oauth_username);
                        $email = $oauth_email;
                        $password = Hash::make(Custom::slugify('', 12));
                        /*if ($email != $oauth_username.'@facebook.com') {
                            $active = 1;
                        } else {
                            $active = 0;
                        }*/
                        $user = $this->create($username, $email, $password, 1);

                        $new_oauth_user = new OauthUser();
                        $new_oauth_user->user_id = $user->id;
                        $new_oauth_user->service = 'facebook';
                        $new_oauth_user->oauth_uid = $oauth_userid;
                        $new_oauth_user->save();

                    } else {
                        // Redirect and send error message that email already exists. Let them know that they can request to reset password if they do not remember
                        return Redirect::to('/')->with(array('status' => 'error', 'message' => Lang::get('words.username-exists')));
                    }
                }
                // Redirect to new User Login;
                Auth::login($user, true);
                return Redirect::to('/')->with(array('status' => 'success', 'message' => Lang::get('words.login-success')));
            }

        } else {
            $url = $fb->getAuthorizationUri();
            // return to facebook login url
            return Redirect::to((string)$url);
        }
    }

    // *********** TWITTER OAUTH SIGNIN/SIGNUP ********** //

    public function loginWithTwitter()
    {
        $settings = Setting::first();

        // get data from input
        $token = Input::get('oauth_token');
        $verify = Input::get('oauth_verifier');

        // get twitter service
        $tw = OAuth::consumer('Twitter');

        // if code is provided get user data and sign in
        if (!empty($token) && !empty($verify)) {
            // This was a callback request from twitter, get the token
            $token = $tw->requestAccessToken($token, $verify);

            // Send a request with it
            $result = json_decode($tw->request('account/verify_credentials.json'), true);

            $oauth_userid = $result['id'];
            $oauth_username = Custom::slugify($result['screen_name']);
            $oauth_email = $oauth_username.'@twitter.com';

            if (isset($oauth_userid) && isset($oauth_username)) {
                $twitter_auth = OauthUser::where('oauth_uid', $oauth_userid)->where('service', 'twitter')->first();

                if (isset($twitter_auth->id)) {
                    $user = User::find($twitter_auth->user_id);
                } else {
                    // Execute Add or Login Oauth User
                    $user = User::where('email', $oauth_email)->first();
                    if (!isset($user->id)) {
                        $username = $this->createUsernameIfExists($oauth_username);
                        $email = $oauth_email;
                        $password = Hash::make(Custom::slugify('', 12));
                        $user = $this->create($username, $email, $password, 1);

                        $new_oauth_user = new OauthUser();
                        $new_oauth_user->user_id = $user->id;
                        $new_oauth_user->service = 'twitter';
                        $new_oauth_user->oauth_uid = $oauth_userid;
                        $new_oauth_user->save();

                    } else {
                        // Redirect and send error message that email already exists. Let them know that they can request to reset password if they do not remember
                        return Redirect::to('/')->with(array('status' => 'error', 'message' => Lang::get('words.username-exists')));
                    }
                }
                // Redirect to new User Login;
                Auth::login($user, true);
                return Redirect::to('/')->with(array('status' => 'success', 'message' => Lang::get('words.login-success')));

            }
        } else {
            // get request token
            $reqToken = $tw->requestRequestToken();
            // get Authorization Uri sending the request token
            $url = $tw->getAuthorizationUri(array('oauth_token' => $reqToken->getRequestToken()));

            //dd($url);
            return Redirect::to((string)$url);
        }
    }

    // *********** GOOGLE OAUTH SIGNIN/SIGNUP ********** //

    public function loginWithGoogle()
    {
        $settings = Setting::first();

        // get data from input
        $code = Input::get('code');

        // get google service
        $googleService = OAuth::consumer('Google');

        // if code is provided get user data and sign in
        if (!empty($code)) {

            // This was a callback request from google, get the token
            $token = $googleService->requestAccessToken($code);

            // Send a request with it
            $result = json_decode($googleService->request('https://www.googleapis.com/oauth2/v1/userinfo'), true);

            $oauth_userid = $result['id'];
            $oauth_username = Custom::slugify($result['name']);
            $oauth_email = isset($result['email']) ? $result['email'] : $oauth_username.'@gmail.com';

            if (isset($oauth_userid) && isset($oauth_username) && isset($oauth_email)) {
                $google_auth = OauthUser::where('oauth_uid', $oauth_userid)->where('service', 'google')->first();

                if (isset($google_auth->id)) {
                    $user = User::find($google_auth->user_id);
                } else {
                    // Execute Add or Login Oauth User
                    $user = User::where('email', $oauth_email)->first();

                    if (!isset($user->id)) {
                        $username = $this->createUsernameIfExists($oauth_username);
                        $email = $oauth_email;
                        $password = Hash::make(Custom::slugify('', 12));
                        /*if ($email != $oauth_username.'@gmail.com') {
                            $active = 1;
                        } else {
                            $active = 0;
                        }*/
                        $user = $this->create($username, $email, $password, 1);

                        $new_oauth_user = new OauthUser();
                        $new_oauth_user->user_id = $user->id;
                        $new_oauth_user->service = 'google';
                        $new_oauth_user->oauth_uid = $oauth_userid;
                        $new_oauth_user->save();

                    } else {
                        // Redirect and send error message that email already exists. Let them know that they can request to reset password if they do not remember
                        return Redirect::to('/')->with(array('status' => 'error', 'message' => Lang::get('words.username-exists')));
                    }
                }
                // Redirect to new User Login;
                Auth::login($user, true);
                return Redirect::to('/')->with(array('status' => 'success', 'message' => Lang::get('words.login-success')));

            }
        } else {
            // get googleService authorization
            $url = $googleService->getAuthorizationUri();

            return Redirect::to((string)$url);
        }
    }

    /**
     * Signup Signup.
     * POST /signup
     *
     * @return Response
     */
    public function signup()
    {

        if(Auth::user()->admin == 1) {
            $validator = Validator::make(Input::all(), static::$adminrules);
        }else{
            $validator = Validator::make(Input::all(), static::$rules);
        }

        if ($validator->fails()) {
            $message = $validator->messages()->all()[0];
            return json_encode(array('status' => 'error', 'message' => $message));
        }

        $username = htmlspecialchars(stripslashes(Input::get('username')));

        $user = User::where('username', '=', $username)->first();

        if (!$user) {
            $email = Input::get('email');
            $activation_token = str_random(64);
            //dd($activation_token);
            $user = $this->create($username, $email, Hash::make(Input::get('password')), 0, $activation_token);
            $data = array('username' => $username, 'activation_token' => $activation_token);

            if(Auth::user()->admin) {
                /*Mail::send(
                    'emails.welcome',
                    $data,
                    function ($message) use ($email) {
                        $message->to($email)->subject(Lang::get('words.activation-subject'));
                    }
                );*/
                return json_encode(array('status' => 'success', 'message' => Lang::get('words.user-registered')));
            }
            return json_encode(array('status' => 'success', 'message' => Lang::get('words.register-success')));
        } else {
            return json_encode(array('status' => 'error', 'message' => Lang::get('words.username-exists')));
        }
    }


    private function createUsernameIfExists($username)
    {
        $user = User::where('username', $username)->first();

        while (isset($user->id)) {
            $username = $username.Custom::slugify('', 4);
            $user = User::where('username', $username)->first();
        }

        return $username;
    }

    public function activation()
    {
        $token = Input::get('token');

        if ($token) {
            $user = User::where('activation_token', $token)->where('active', 0)->first();

            if ($user) {
                $user->activation_token = null;
                $user->active = 1;
                $user->save();

                return Redirect::to('/')->with(array('status' => 'showSuccessToast', 'message'  => Lang::get('words.email-verified')));
            } else {
                return Redirect::to('/')->with(array('status' => 'showErrorToast', 'message'  => Lang::get('words.email-already-verified')));
            }
        }
    }
}
