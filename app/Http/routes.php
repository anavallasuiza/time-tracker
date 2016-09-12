<?php

Route::any('/login', ['as' => 'login', 'uses' => 'Home@login']);

Route::get('/401', ['as' => 'error.401', 'uses' => 'Home@error401']);
Route::get('/500', ['as' => 'error.401', 'uses' => 'Home@error500']);
Route::get('/404', ['as' => 'error.404', 'uses' => 'Home@error404']);


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

Route::group(['before' => 'auth'], function () {
    Route::get('/logout', ['as' => 'logout', 'uses' => 'Auth\AuthController@getLogout']);


    Route::any('/', ['as' => 'index', 'uses' => 'Home@index']);

    Route::get('/stats', ['as' => 'stats.index', 'uses' => 'Home@stats']);

    Route::get('/stats/calendar', ['as' => 'stats.calendar', 'uses' => 'Home@statsCalendar']);

    Route::any('/sync', ['as' => 'sync', 'uses' => 'Home@sync']);

    Route::any('/edit', [
        'as' => 'edit.show',
        'uses' => 'Home@edit'
    ]);

    Route::any('/activity/', ['as' => 'activity.add', 'uses' => 'Home@activityAdd']);

    Route::any('/activity/{id}', [
        'as' => 'activity.edit',
        'uses' => 'Home@activityEdit'
    ]);

    Route::any('/tag/', ['as' => 'tag.add', 'uses' => 'Home@tagAdd']);
    Route::any('/tag/{id}', ['as' => 'activity.edit', 'uses' => 'Home@tagEdit']);

    Route::any('/user/', ['as' => 'user.add', 'uses' => 'Home@userAdd']);
    Route::any('/user/{id}', ['as' => 'user.edit', 'uses' => 'Home@userEdit']);

    Route::get('/client', [
        'as' => 'client.add',
        'uses' => 'ClientController@add'
    ]);
    Route::post('/client', [
        'uses' => 'ClientController@postAdd'
    ]);
    Route::get('/client/{id}', [
        'as' => 'client.edit',
        'uses' => 'ClientController@edit'
    ]);
    Route::post('/client/{id}', [
        'as' => 'client.edit',
        'uses' => 'ClientController@postEdit'
    ]);


    Route::get('/fact-tr/{id}', ['as' => 'fact.tr', 'uses' => 'Home@factTr']);

    Route::get('/dump-sql', ['as' => 'maintenance.sql-dump', 'uses' => 'Home@sqlDownload']);
    Route::any('/git-update', ['as' => 'maintenance.git-update', 'uses' => 'Home@gitUpdate']);
    Route::any('/tools-duplicates', ['as' => 'maintenance.duplicates', 'uses' => 'Home@toolsDuplicates']);

    Route::get('/notifications', ['as' => 'notifications.index', 'uses' => 'Home@notifications']);
    Route::post('/notifications/{id}/read', ['as' => 'notifications.read', 'uses' => 'Home@notificationRead']);
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
