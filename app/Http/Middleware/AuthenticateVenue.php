<?php 
namespace OurScene\Http\Middleware;

use Closure;

use Session;
use Redirect;

class AuthenticateVenue {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		if(Session::get('user_type') != 'venue')
			return abort(401);

		return $next($request);
	}

}