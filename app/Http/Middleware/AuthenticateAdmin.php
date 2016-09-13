<?php

namespace App\Http\Middleware;

use App\Database\Models\User;
use Closure;
use Illuminate\Contracts\Auth\Guard;

class AuthenticateAdmin
{
    /**
     * The guard instance.
     *
     * @var \Illuminate\Contracts\Auth\Guard
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Guard $auth
     * @return void
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /** @var User $user */
        $user = $this->auth->user();

        if (empty($user) || (!empty($user) && !$user->isAdmin())) {
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest(route('v2.time.index'));
            }
        }

        return $next($request);
    }
}
