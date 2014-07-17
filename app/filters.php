<?php
use App\Libs;

Route::filter('auth', function()
{
    if (!Libs\Auth::guest()) {
        return;
    }

    return Libs\Auth::unauthorized();
});

Route::filter('auth.api', function()
{
    return (($response = Libs\Auth::login('api')) === true) ? null : $response;
});

Route::filter('guest', function()
{
    if (Auth::check()) return Redirect::to('/');
});

Route::filter('csrf', function()
{
    if (Session::token() != Input::get('_token')) {
        throw new Illuminate\Session\TokenMismatchException;
    }
});
