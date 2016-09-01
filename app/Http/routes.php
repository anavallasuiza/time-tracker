<?php

Route::any('/login', 'Home@login');

Route::get('/401', 'Home@error401');
Route::get('/404', 'Home@error404');

Route::group(['before' => 'auth'], function()
{
    Route::any('/', 'Home@index');
    Route::get('/stats', 'Home@stats');
    Route::get('/stats/calendar', 'Home@statsCalendar');
    Route::any('/sync', 'Home@sync');
    Route::any('/edit', 'Home@edit');
    Route::any('/activity/', 'Home@activityAdd');
    Route::any('/activity/{id}', 'Home@activityEdit');
    Route::any('/tag/', 'Home@tagAdd');
    Route::any('/tag/{id}', 'Home@tagEdit');
    Route::any('/user/', 'Home@userAdd');
    Route::any('/user/{id}', 'Home@userEdit');
    Route::get('/fact-tr/{id}', 'Home@factTr');
    Route::get('/dump-sql', 'Home@sqlDownload');
    Route::any('/git-update', 'Home@gitUpdate');
    Route::any('/tools-duplicates', 'Home@toolsDuplicates');
    Route::get('/notifications', 'Home@notifications');
    Route::post('/notifications/{id}/read', 'Home@notificationRead');
});

Route::group(['prefix' => 'api', 'before' => 'auth.api'], function()
{
    Route::get('activities', 'Api@getActivities');
    Route::get('facts', 'Api@getFacts');
    Route::get('tags', 'Api@getTags');
    Route::get('facts-tags', 'Api@getFactsTags');

    Route::post('activities', 'Api@setActivities');
    Route::post('facts', 'Api@setFacts');
    Route::post('tags', 'Api@setTags');
    Route::post('facts-tags', 'Api@setFactsTags');

    Route::delete('facts', 'Api@deleteFacts');
});
