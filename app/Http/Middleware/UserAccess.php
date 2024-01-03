<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UserAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next,$user_access_id)
    {
        if ($request->user()->user_access_id != $user_access_id) {
            return redirect()->to('/application-room-request')->with('message-error','Access Denied!');
        }
        return $next($request);
    }
}
