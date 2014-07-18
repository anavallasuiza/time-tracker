<?php
use App\Libs;

Route::filter('auth', function()
{
    return (($response = Libs\Auth::login()) === true) ? null : $response;
});

Route::filter('auth.api', function()
{
    if (!Request::isJson()) {
        return Response::json(array(
            'code' =>  401,
            'message' => _('Unauthorized')
        ), 401);
    }

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
