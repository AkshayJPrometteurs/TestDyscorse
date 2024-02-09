<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next) : Response
    {
      	/*if ($request->hasHeader('X-User-Timezone')) {
            $userTimeZone = $request->header('X-User-Timezone');
            config(['app.timezone' => $userTimeZone]);
        }*/
        $request->validate(['user_id'=>'required']);
        $user = User::find($request->user_id);
        if($user){
            return $next($request);
        }
      	
        return response()->json(['status'=>500,'data'=>[],'message'=>'User Account Not Found']);
    }
}
