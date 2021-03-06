<?php

Route::get('/login', ['as' => 'login', 'uses' => 'Auth\AuthController@getLogin']);
Route::post('/login', ['as' => 'login.post', 'uses' => 'Auth\AuthController@postLogin']);
Route::get('/logout', ['as' => 'logout', 'uses' => 'Auth\AuthController@getLogout']);

Route::get('/', ['as' => 'index', 'uses' => 'IndexController@index']);

Route::group([
    'middleware' => ['auth']
], function () {


    Route::group([
        'prefix' => 'time/',
    ], function () {
        Route::get('/', ['as' => 'time.index', 'uses' => 'TimeController@index']);
        Route::post('/fact/edit', ['as' => 'time.fact.edit','uses' => 'TimeController@updateFact']);
        Route::post('/fact/add', ['as' => 'time.fact.add','uses' => 'TimeController@addFact']);
        Route::get('/fact/{id}', ['as' => 'time.fact.item','uses' => 'TimeController@getFact']);
    });

    Route::group([
       //'middleware'=>['auth.admin'],
        'prefix' => 'edit/',
    ], function () {
        Route::get('/', ['as' => 'edit.index', 'uses' => 'Admin\EditController@index']);

        Route::group([
            //'middleware'=>['auth.admin'],
            'prefix' => 'client/',
        ],function () {

            Route::get('/{id}', [
                'as' => 'edit.client.edit',
                'uses' => 'Admin\ClientController@edit'
            ]);
            Route::post('/{id}', [
                'uses' => 'Admin\ClientController@postEdit'
            ]);
            Route::get('/', [
                'as' => 'edit.client.add',
                'uses' => 'Admin\ClientController@add'
            ]);
            Route::post('/', [
                'uses' => 'Admin\ClientController@postAdd'
            ]);
        });

        Route::group([
            //'middleware'=>['auth.admin'],
            'prefix' => 'user/',
        ],function () {

            Route::get('/{id}', [
                'as' => 'edit.user.edit',
                'uses' => 'Admin\UserController@edit'
            ]);
            Route::post('/{id}', [
                'uses' => 'Admin\UserController@postEdit'
            ]);
            Route::get('/', [
                'as' => 'edit.user.add',
                'uses' => 'Admin\UserController@add'
            ]);
            Route::post('/', [
                'uses' => 'Admin\UserController@postAdd'
            ]);
        });


        Route::group([
            //'middleware'=>['auth.admin'],
            'prefix' => 'tag/',
        ],function () {

            Route::get('/{id}', [
                'as' => 'edit.tag.edit',
                'uses' => 'Admin\TagController@edit'
            ]);
            Route::post('/{id}', [
                'uses' => 'Admin\TagController@postEdit'
            ]);
            Route::get('/', [
                'as' => 'edit.tag.add',
                'uses' => 'Admin\TagController@add'
            ]);
            Route::post('/', [
                'uses' => 'Admin\TagController@postAdd'
            ]);
        });

        Route::group([
            //'middleware'=>['auth.admin'],
            'prefix' => 'activity/',
        ],function () {

            Route::get('/{id}', [
                'as' => 'edit.activity.edit',
                'uses' => 'Admin\ActivityController@edit'
            ]);
            Route::post('/{id}', [
                'uses' => 'Admin\ActivityController@postEdit'
            ]);
            Route::get('/', [
                'as' => 'edit.activity.add',
                'uses' => 'Admin\ActivityController@add'
            ]);
            Route::post('/', [
                'uses' => 'Admin\ActivityController@postAdd'
            ]);
        });

    });

    Route::group([
        'prefix' => 'stats/',
    ], function () {
        Route::get('/', ['as' => 'stats.index', 'uses' => 'StatsController@index']);
        Route::get('/calendar', ['as' => 'stats.calendar', 'uses' => 'StatsController@calendar']);
    });

    Route::group([
        'prefix' => 'notifications/',
    ], function () {
        Route::get('/', ['as' => 'notifications.index', 'uses' => 'NotificationsController@index']);
        Route::post('/read/{id}',['as' => 'notifications.read', 'uses' => 'NotificationsController@read']);
    });
});

Route::group(['prefix' => 'api/', 'before' => 'auth.api'], function () {
    Route::get('activities', ['as' => 'api.activities', 'uses' => 'Api@getActivities']);
    Route::get('facts', ['as' => 'api.facts', 'uses' => 'Api@getFacts']);
    Route::get('tags', ['as' => 'api.tags', 'uses' => 'Api@getTags']);
    Route::get('facts-tags', ['as' => 'api.facts.tags', 'uses' => 'Api@getFactsTags']);

    Route::post('activities', ['as' => 'api.activities.post', 'uses' => 'Api@setActivities']);
    Route::post('facts', ['as' => 'api.facts.post', 'uses' => 'Api@setFacts']);
    Route::post('tags', ['as' => 'api.tags.post', 'uses' => 'Api@setTags']);
    Route::post('facts-tags', ['as' => 'api.facts-tags.post', 'uses' => 'Api@setFactsTags']);

    Route::delete('facts', ['as' => 'api.facts.delete', 'uses' => 'Api@deleteFacts']);
});
