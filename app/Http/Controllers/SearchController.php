<?php namespace OurScene\Http\Controllers;

use Log;
use Session;
use View;
use Input;
use Redirect;
use App;
use Response;
use MongoDate;
use DB;

use OurScene\Models\User;
use OurScene\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Support\Collection;

use OurScene\Helpers\PaypalHelper;

class SearchController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Search Controller
	|--------------------------------------------------------------------------
	|
	| This controller manages all search queries and results.
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('auth.login');
	}

	/* Search */

	public function getSearch(){
		// Index view for search
		Input::merge(array_map('trim', Input::all()));
		$input = filter_var_array(Input::all(), FILTER_SANITIZE_STRIPPED);
		$name = null;
		$genre =null;
		$locality =null;
		$search_results = true;
		$isSingleParam = false;
		$singleParam = null;
		if (isset($input['params'])) {
			$singleParam = $input['params'];
			$name = $singleParam;
			$genre = $singleParam;
			$locality = $singleParam;
			$isSingleParam = true;
			$all = User::searchAll($name, $genre, $locality)->isActive()->paginate(6);
			$all->setPath(env('PAGINATE_URI') . '/search');
		}else{
			$all = User::isActive()->paginate(6);
			$all->setPath(env('PAGINATE_URI') . '/search');
		}

		return View::make('ourscene.search', compact('all','search_results','name','genre','locality','isSingleParam','singleParam'));
	}

	/* Search results */

	public function getSearchResults(Request $request){

		//trim and sanitize all inputs
		Input::merge(array_map('trim', Input::all()));
		$input = filter_var_array(Input::all(), FILTER_SANITIZE_STRIPPED);

		// parameters
		$name = $input['param'];
		$genre = $input['param'];
		$locality = $input['param'];

		// pagination
		$artists = User::searchArtists($name, $genre, $locality)->isActive()->take(5)->get();
		$venues = User::searchVenues($name, $genre, $locality)->isActive()->take(5)->get();

		$search_results = true;
		return response()->json(compact('name', 'genre', 'locality', 'artists', 'venues', 'search_results'));
	}
}
