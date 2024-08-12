<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
class UserMahjconApi
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
            # fetch The User Id
            $userId = $request->header('id');

            # get Request Token
            $authorizationToken = $request->header('Authorization1');

            if(empty($userId)){
            return response()->json([
                                  'message' => 'Header id is required',
                                  'code' => '402',
                               ]);  
            }

            if(empty($authorizationToken)){
            return response()->json([
                                  'message' => 'Authorization token is required',
                                  'code' => '402',
                               ]);  
            }

            # get the user Token
            $apiToken = User::where('id', $userId)->pluck('api_token')->first();


            # Chek Api Token for vallidation
            if($apiToken == $authorizationToken){
            return $next($request);
            }

            return response()->json([
            'message' => 'Unauthenticated User.',
            'code' => '402',
            ]);

            return $next($request);
    }
}
