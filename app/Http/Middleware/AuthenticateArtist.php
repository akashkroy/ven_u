<?php 
namespace OurScene\Http\Middleware;

use Closure;

use Session;
use Redirect;

class AuthenticateArtist {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		if(Session::get('user_type') != 'artist')
			return abort(401);

		return $next($request);
	}

}