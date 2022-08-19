<?php

namespace App\Http\Middleware\Auth;

use Closure;
use Auth;

class AuthenticateAdmin {
	
	public function handle($request, Closure $next, $guard = null){
		$user = Auth::user();
		if($user->userType != 0){
			return redirect()->name("home");
		}
		
		return $next($request);
		
	}
}



