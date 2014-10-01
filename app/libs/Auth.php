<?php
namespace App\Libs;

use Config, Input, Request, Response, Redirect;

class Auth extends \Auth
{
    public static function user()
    {
        if ($user = parent::user()) {
            $user->dateFormat = 'd/m/Y'.($user->store_hours ? ' H:i' : '');
        }

        return $user;
    }

    public static function login($method = '')
    {
        if (empty($method)) {
            return call_user_func('self::login'.Config::get('auth')['method']);
        } else {
            return call_user_func('self::login'.$method);
        }
    }

    private static function loginHtml()
    {
        if (Input::get('action') === 'login') {
            return self::loginUserPassword(Input::get('user'), Input::get('password'));
        }
    }

    private static function loginBasic()
    {
        if (!isset($_SERVER['PHP_AUTH_USER'])) {
            return (new \App\Controllers\Home)->error401();
        }

        return self::loginUserPassword($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'], '/401');
    }

    private static function loginUserPassword($user, $password, $path = '')
    {
        if (empty($user) || empty($password)) {
            if (self::guest()) {
                return self::unauthorized(_('User or password is not correct'), $path);
            }

            return true;
        }

        if (self::viaRemember()) {
            return true;
        }

        $success = self::attempt([
            'user' => $user,
            'password' => $password
        ], true);

        if ($success !== true) {
            return self::unauthorized(_('User or password is not correct'), $path);
        }

        if (empty(self::user()->enabled)) {
            return self::unauthorized(_('Sorry but your user is disabled. Please contact with us to solve this problem.'), $path);
        }

        return true;
    }

    private static function loginApi()
    {
        if (empty($user = Input::get('user')) || empty($secret = Input::get('secret'))) {
            return self::unauthorized();
        }

        $user = \App\Models\Users::where('user', '=', $user)->where('api_key', '=', $secret)->first();

        if (empty($user)) {
            return self::unauthorized(_('User or password is not correct'), '/401');
        }

        if (empty($user->enabled)) {
            return self::unauthorized(_('Sorry but your user is disabled. Please contact with us to solve this problem.'), '/401');
        }

        \Auth::loginUsingId($user->id);

        return true;
    }

    public static function unauthorized($message = '', $path = '')
    {
        self::logout();

        if (Request::ajax()) {
            return Response::make('Unauthorized', 401);
        } elseif (Request::isJson()) {
            return Response::json(array(
                'code' =>  401,
                'message' => _('Unauthorized')
            ), 401);
        }

        $path = $path ?: '/login';

        if ($path === '/login') {
            $redirect = Redirect::guest($path);
        } else {
            $redirect = Redirect::to($path);
        }

        if (empty($message)) {
            return $redirect;
        }

        return $redirect->with('flash-message', [
            'message' => $message,
            'status' => 'danger'
        ]);
    }
}
