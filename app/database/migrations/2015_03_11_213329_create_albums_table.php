<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAlbumsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('albums', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('artist_id');
			$table->integer('category_id');
			$table->integer('language_id');
			$table->string('title', 200);
			$table->string('slug', 10);
			$table->string('location');
			$table->integer('views')->default(0);
			$table->integer('downloads')->default(0);
			$table->date('release_date');

			$table->timestamps();
		});
        Schema::table('albums', function($table)
        {
            $table->integer('featured')->default(0)->after('release_date');
        });
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('albums');
	}

}
