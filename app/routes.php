<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::any('/login', 'App\Controllers\Home@login');

Route::get('/401', 'App\Controllers\Home@error401');
Route::get('/404', 'App\Controllers\Home@error404');

Route::group(['before' => 'auth'], function()
{
    Route::any('/', 'App\Controllers\Home@index');
    Route::get('/stats', 'App\Controllers\Home@stats');
    Route::any('/sync', 'App\Controllers\Home@sync');
    Route::any('/edit', 'App\Controllers\Home@edit');
    Route::any('/activity/', 'App\Controllers\Home@activityAdd');
    Route::any('/activity/{id}', 'App\Controllers\Home@activityEdit');
    Route::any('/tag/', 'App\Controllers\Home@tagAdd');
    Route::any('/tag/{id}', 'App\Controllers\Home@tagEdit');
    Route::any('/user/', 'App\Controllers\Home@userAdd');
    Route::any('/user/{id}', 'App\Controllers\Home@userEdit');
    Route::get('/fact-tr/{id}', 'App\Controllers\Home@factTr');
    Route::get('/dump-sql', 'App\Controllers\Home@sqlDownload');
    Route::any('/git-update', 'App\Controllers\Home@gitUpdate');
});

Route::group(['prefix' => 'api', 'before' => 'auth.api'], function()
{
    Route::get('activities', 'App\Controllers\Api@getActivities');
    Route::get('facts', 'App\Controllers\Api@getFacts');
    Route::get('tags', 'App\Controllers\Api@getTags');
    Route::get('facts-tags', 'App\Controllers\Api@getFactsTags');

    Route::post('activities', 'App\Controllers\Api@setActivities');
    Route::post('facts', 'App\Controllers\Api@setFacts');
    Route::post('tags', 'App\Controllers\Api@setTags');
    Route::post('facts-tags', 'App\Controllers\Api@setFactsTags');

    Route::delete('facts', 'App\Controllers\Api@deleteFacts');
});
