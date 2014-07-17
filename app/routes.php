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
    Route::get('/', 'App\Controllers\Home@index');
    Route::get('/dump-sql', 'App\Controllers\Home@dumpSQL');
    Route::get('/git-update', 'App\Controllers\Home@gitUpdate');
});

Route::group(['prefix' => 'api', 'before' => 'auth.api'], function()
{
    Route::get('activities', 'App\Controllers\Api@getActivities');
    Route::get('categories', 'App\Controllers\Api@getCategories');
    Route::get('facts', 'App\Controllers\Api@getFacts');
    Route::get('tags', 'App\Controllers\Api@getTags');
    Route::get('facts-tags', 'App\Controllers\Api@getFactsTags');

    Route::post('activities', 'App\Controllers\Api@setActivities');
    Route::post('categories', 'App\Controllers\Api@setCategories');
    Route::post('facts', 'App\Controllers\Api@setFacts');
    Route::post('tags', 'App\Controllers\Api@setTags');
    Route::post('facts-tags', 'App\Controllers\Api@setFactsTags');

    Route::delete('facts', 'App\Controllers\Api@deleteFacts');
});
