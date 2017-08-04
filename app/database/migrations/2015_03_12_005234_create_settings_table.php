<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSettingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('settings', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('website_name');
			$table->string('website_title');
			$table->string('website_description');
			$table->string('theme_color');
			$table->string('fb_key');
			$table->string('fb_secret_key');
			$table->string('fb_page_id');
			$table->string('google_key');
			$table->string('google_secret_key');
			$table->string('google_page_id');
			$table->string('recaptcha_site_key');
			$table->string('recaptcha_secret_key');
			$table->string('twitter_key');
			$table->string('twitter_secret_key');
			$table->string('twitter_page_id');
			$table->boolean('downloadable')->default(1);
			$table->boolean('auth_download')->default(0);
			$table->boolean('zip_download')->default(1);
			$table->text('box_ad');
			$table->text('banner_ad');
			$table->text('analytics');
			$table->string('youtube_key');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('settings');
	}

}
