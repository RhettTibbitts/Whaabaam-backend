<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\UserApiToken;

class VerifyApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {   
        //check is token valid or not
        if(isset($_SERVER['HTTP_TOKEN'])){
            
            //commented just for testing
            /*$count = UserApiToken::where('token','LIKE',$_SERVER['HTTP_TOKEN'])->count();
            if($count == 0){
                return response()->json([
                    'status' => 203,
                    'message'=> __('messages.api auth err') 
                ]); 
            }*/ 
        } else{
            return response()->json([
                'status' => 203,
                'message'=> __('messages.api auth err') 
            ]); 
        }

        return $next($request);
    }
}
