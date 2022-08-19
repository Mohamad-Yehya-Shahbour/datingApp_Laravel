<?php

namespace App\Http\Middleware\Auth;

use Closure;
use Auth;

class RedirectIfAuthenticated {
	
	public function handle($request, Closure $next, $guard = null){
		
		if(Auth::user()){
			return redirect()->route("home");
		}
		
		return $next($request);
		
	}
}



