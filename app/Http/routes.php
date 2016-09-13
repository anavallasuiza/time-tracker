<?php

Route::get('/login', ['as' => 'login', 'uses' => 'Auth\AuthController@getLogin']);
Route::post('/login', ['as' => 'login.post', 'uses' => 'Auth\AuthController@postLogin']);
Route::get('/logout', ['as' => 'logout', 'uses' => 'Auth\AuthController@getLogout']);

Route::get('/401', ['as' => 'error.401', 'uses' => 'Home@error401']);
Route::get('/500', ['as' => 'error.401', 'uses' => 'Home@error500']);
Route::get('/404', ['as' => 'error.404', 'uses' => 'Home@error404']);


Route::get('/', ['as' => 'index', 'uses' => 'V2\IndexController@index']);

Route::group([
    'middleware' => ['auth'],
    'prefix' => 'v2/',
], function () {


    Route::group([
        'prefix' => 'time/',
    ], function () {
        Route::get('/', ['as' => 'v2.time.index', 'uses' => 'V2\TimeController@index']);
        Route::post('/fact/edit', ['as' => 'v2.time.fact.edit','uses' => 'V2\TimeController@updateFact']);
        Route::post('/fact/add', ['as' => 'v2.time.fact.add','uses' => 'V2\TimeController@addFact']);
    });

    Route::group([
       //'middleware'=>['auth.admin'],
        'prefix' => 'edit/',
    ], function () {
        Route::get('/', ['as' => 'v2.edit.index', 'uses' => 'V2\Admin\EditController@index']);

        Route::group([
            //'middleware'=>['auth.admin'],
            'prefix' => 'client/',
        ],function () {

            Route::get('/{id}', [
                'as' => 'v2.edit.client.edit',
                'uses' => 'V2\Admin\ClientController@edit'
            ]);
            Route::post('/{id}', [
                'uses' => 'V2\Admin\ClientController@postEdit'
            ]);
            Route::get('/', [
                'as' => 'v2.edit.client.add',
                'uses' => 'V2\Admin\ClientController@add'
            ]);
            Route::post('/', [
                'uses' => 'V2\Admin\ClientController@postAdd'
            ]);
        });

        Route::group([
            //'middleware'=>['auth.admin'],
            'prefix' => 'user/',
        ],function () {

            Route::get('/{id}', [
                'as' => 'v2.edit.user.edit',
                'uses' => 'V2\Admin\UserController@edit'
            ]);
            Route::post('/{id}', [
                'uses' => 'V2\Admin\UserController@postEdit'
            ]);
            Route::get('/', [
                'as' => 'v2.edit.user.add',
                'uses' => 'V2\Admin\UserController@add'
            ]);
            Route::post('/', [
                'uses' => 'V2\Admin\UserController@postAdd'
            ]);
        });


        Route::group([
            //'middleware'=>['auth.admin'],
            'prefix' => 'tag/',
        ],function () {

            Route::get('/{id}', [
                'as' => 'v2.edit.tag.edit',
                'uses' => 'V2\Admin\TagController@edit'
            ]);
            Route::post('/{id}', [
                'uses' => 'V2\Admin\TagController@postEdit'
            ]);
            Route::get('/', [
                'as' => 'v2.edit.tag.add',
                'uses' => 'V2\Admin\TagController@add'
            ]);
            Route::post('/', [
                'uses' => 'V2\Admin\TagController@postAdd'
            ]);
        });

        Route::group([
            //'middleware'=>['auth.admin'],
            'prefix' => 'activity/',
        ],function () {

            Route::get('/{id}', [
                'as' => 'v2.edit.activity.edit',
                'uses' => 'V2\Admin\ActivityController@edit'
            ]);
            Route::post('/{id}', [
                'uses' => 'V2\Admin\ActivityController@postEdit'
            ]);
            Route::get('/', [
                'as' => 'v2.edit.activity.add',
                'uses' => 'V2\Admin\ActivityController@add'
            ]);
            Route::post('/', [
                'uses' => 'V2\Admin\ActivityController@postAdd'
            ]);
        });

    });

    Route::group([
        'prefix' => 'stats/',
    ], function () {
        Route::get('/', ['as' => 'v2.stats.index', 'uses' => 'V2\StatsController@index']);
        Route::get('/calendar', ['as' => 'v2.stats.calendar', 'uses' => 'V2\StatsController@calendar']);
    });

    Route::group([
        'prefix' => 'maintenance/',
    ], function () {
        Route::get('/sync', ['as' => 'v2.maintenance.sync', 'uses' => 'V2\MaintenanceController@sync']);
        Route::post('/sync', ['uses' => 'V2\MaintenanceController@doSync']);
    });

    Route::group([
        'prefix' => 'notifications/',
    ], function () {
        Route::get('/', ['as' => 'v2.notifications.index', 'uses' => 'V2\NotificationsController@index']);
        Route::post('/read/{id}',['as' => 'v2.notifications.read', 'uses' => 'V2\NotificationsController@read']);
    });
});

Route::group(['prefix' => 'api/', 'before' => 'auth.api'], function () {
    Route::get('activities', ['as' => 'api.activities', 'uses' => 'Api@getActivities']);
    Route::get('facts', ['as' => 'api.facts', 'uses' => 'Api@getFacts']);
    Route::get('tags', ['as' => 'api.tags', 'uses' => 'Api@getTags']);
    Route::get('facts-tags', ['as' => 'api.facts.tags', 'uses' => 'Api@getFactsTags']);

    Route::post('activities', ['uses' => 'Api@setActivities']);
    Route::post('facts', ['as' => 'notifications.read', 'uses' => 'Api@setFacts']);
    Route::post('tags', ['as' => 'notifications.read', 'uses' => 'Api@setTags']);
    Route::post('facts-tags', ['as' => 'notifications.read', 'uses' => 'Api@setFactsTags']);

    Route::delete('facts', ['uses' => 'Api@deleteFacts']);
});
