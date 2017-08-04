<?php

class SettingsTableSeeder extends Seeder {

	/**
	 * Auto generated seed file
	 *
	 * @return void
	 */
	public function run()
	{
		\DB::table('settings')->truncate();
        
		\DB::table('settings')->insert(array (
			0 => 
			array (
				'id' => 1,
				'website_name' => 'BinaryZeal',
				'website_title' => 'Thinking in Binary',
				'website_description' => 'Free MP3 Downloads',
				'theme_color'	=>	'#ff3e18',
				'fb_key' => '1648829325394809',
				'fb_secret_key' => '774d5f3b62fbde79a3d2236f5f503cd5',
				'fb_page_id' => 'BinaryZeal',
				'google_key' => '378013601138-s7sd7l3nmqlonl6fd2ack3a8i9ii72ec.apps.googleusercontent.com',
				'google_secret_key' => 'Dp3VbIyvw2U3JnTBdOqOHs-p',
				'google_page_id' => '100049171812671088644',
				'recaptcha_site_key' => '6LcvRP8SAAAAAHWh7TrdZoD3AGKuV5YRtkX9ZpUN',
				'recaptcha_secret_key' => '6LewH-YSAAAAAF-M_w4LN_csEumr-ItZF1qDkYgc',
				'twitter_key' => 'YpMtQgNAIXmMg1fzKYjrShU4Z',
				'twitter_secret_key' => 'JxQ6eVRbEZclQoInE0R1eOfNcnP34wTCDAZRgh03xWMbIeW3Z7',
				'twitter_page_id' => 'BinaryZeal',
				'downloadable' => 1,
				'auth_download'	=>	0,
				'zip_download'	=>	1,
				'analytics' => '',
				'box_ad'	=>	'',
				'youtube_key' => 'AIzaSyCSYn85k54O6ZRHhjjVuU3itvxCKuZTha4',
			),
		));
	}

}
