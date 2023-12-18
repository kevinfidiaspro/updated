<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
class UserMutation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user is authenticated

            // Get the current authenticated user
            $user = $request->user();
            if($user->role ==1){
                $id = $request->header('User_id');
                if($id != null && $id != $user->id){
                    $user_new = User::find($id);
                    if($user_new){
                        $request->setUserResolver(function () use ($user_new) {
                            return $user_new;
                        });
                    }
                }
            }         
        

        // Continue to the next middleware/controller
        return $next($request);
    }
}
