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
            $table->string('name');

            $table->timestamps();

            $table->integer('id_categories')->unsigned();
        });

        Schema::create('categories', function($table)
        {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->string('name');

            $table->timestamps();
        });

        Schema::create('facts', function($table)
        {
            $table->engine = 'InnoDB';

            $table->increments('id');

            $table->timestamp('start_time');
            $table->timestamp('end_time');
            $table->integer('total_time');

            $table->text('description');
            $table->string('hostname');

            $table->integer('remote_id')->unsigned();

            $table->timestamps();
            $table->softDeletes();

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

        Schema::create('logs', function($table)
        {
            $table->engine = 'InnoDB';

            $table->increments('id');

            $table->timestamp('date');
            $table->string('description');

            $table->integer('id_facts')->unsigned();
            $table->integer('id_users')->unsigned();
        });

        Schema::create('tags', function($table)
        {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->string('name');

            $table->timestamps();
        });

        Schema::create('users', function($table)
        {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->string('name')->unique();
            $table->string('user')->unique();
            $table->string('email');
            $table->string('password');
            $table->string('password_token');
            $table->string('remember_token');
            $table->string('api_key');
            $table->boolean('admin');
            $table->boolean('enabled');

            $table->timestamps();
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

        Schema::table('logs', function($table)
        {
            $table->foreign('id_facts')
                ->references('id')
                ->on('facts');

            $table->foreign('id_users')
                ->references('id')
                ->on('users');
        });

        Schema::table('facts_tags', function($table)
        {
            $table->foreign('id_facts')
                ->references('id')
                ->on('facts')
                ->onDelete('cascade');

            $table->foreign('id_tags')
                ->references('id')
                ->on('tags')
                ->onDelete('cascade');
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
        Schema::drop('logs');
        Schema::drop('tags');
        Schema::drop('users');
	}
}
