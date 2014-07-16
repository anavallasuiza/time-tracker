<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimeTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('activities', function($table)
        {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->text('name');

            $table->integer('id_categories')->unsigned();
        });

        Schema::create('categories', function($table)
        {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->text('name');
        });

        Schema::create('facts', function($table)
        {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('remote_id')->unsigned();

            $table->timestamp('start_time');
            $table->timestamp('end_time');
            $table->text('description');

            $table->integer('id_activities')->unsigned();
            $table->integer('id_users')->unsigned();
        });

        Schema::create('facts_tags', function($table)
        {
            $table->engine = 'InnoDB';

            $table->increments('id');

            $table->integer('id_facts')->unsigned();
            $table->integer('id_tags')->unsigned();
        });

        Schema::create('tags', function($table)
        {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->text('name');
        });

        Schema::create('users', function($table)
        {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->text('name');
            $table->text('email');
            $table->text('hash');
        });

        Schema::table('activities', function($table)
        {
            $table->foreign('id_categories')
                ->references('id')
                ->on('categories');
        });

        Schema::table('facts', function($table)
        {
            $table->foreign('id_activities')
                ->references('id')
                ->on('activities');

            $table->foreign('id_users')
                ->references('id')
                ->on('users');
        });

        Schema::table('facts_tags', function($table)
        {
            $table->foreign('id_facts')
                ->references('id')
                ->on('facts');

            $table->foreign('id_tags')
                ->references('id')
                ->on('tags');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('facts_tags');
        Schema::drop('facts');
        Schema::drop('activities');
        Schema::drop('categories');
        Schema::drop('tags');
        Schema::drop('users');
	}

}
