<?php 
namespace OurScene\Http\Middleware;

use Closure;

use Session;
use Redirect;

class AuthenticateLogin {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		if(!Session::has('id'))
			return Redirect::to('/login');

		return $next($request);
	}

}
