<?php 
$settings = Setting::first();
return array( 
	
	/*
	|--------------------------------------------------------------------------
	| oAuth Config
	|--------------------------------------------------------------------------
	*/

	/**
	 * Storage
	 */
	'storage' => 'Session', 

	/**
	 * Consumers
	 */
	'consumers' => array(

		/**
		 * Facebook
		 */
        'Facebook' => array(
            'client_id'     => $settings->fb_key,
            'client_secret' => $settings->fb_secret_key,
            'scope'         => array('email'),
        ),

        /**
		 * Google
		 */
        'Google' => array(
		    'client_id'     => $settings->google_key,
		    'client_secret' => $settings->google_secret_key,
		    'scope'         => array('userinfo_email', 'userinfo_profile'),
		),

        /**
		 * Twitter
		 */
		'Twitter' => array(
		    'client_id'     => $settings->twitter_key,
		    'client_secret' => $settings->twitter_secret_key,
		    'scope'         => array(),
		),
	)

);