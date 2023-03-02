<?php

namespace App\Http\Middleware;

use App\Settings;
use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param \Illuminate\Http\Request $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            return route('login');
        }
    }

    public function handle($request, Closure $next, $guard = null)
    {
        $value = Settings::where('key', 'allowed_ipaddress')->first();
        $role = 'user';
        $user = Auth::user();

        $ipaddress_arr = [];
        if (isset($user)) {
            $role = Auth::user()->getRoleNames()[0];
        }
        if(!is_null($value)){
            $ipaddress_arr = explode(',', $value->value);
        }
        if ($role != 'super-admin' && !in_array($request->ip(), $ipaddress_arr)) {
            if(isset($user->ip_restriction) && $user->ip_restriction ==1){

                Session::flush();
                Auth::logout();
            }
        }

        if (Auth::user() && Auth::user()->singleUserToken != session('singleUserToken') && Auth::user()->loggedInStatus == 'Active') {
            Session::flush();
            Auth::logout();
        }

        return parent::handle($request, $next, $guard); // TODO: Change the autogenerated stub
    }
}
