<?php namespace OurScene\Http\Controllers;

use Log;
use Session;
use View;
use Input;
use Redirect;
use Hash;
use App;
use Response;

use Illuminate\Http\Request;
use Illuminate\Contracts\Filesystem\Filesystem;

use OurScene\Models\Equipment;
use OurScene\Models\User;

class EquipmentController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Equipment Controller
	|--------------------------------------------------------------------------
	|
	| This controller manages all needed equipment.
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('auth.login', ['except' => ['getEquipment', 'getAllEquipment', 'getAllEquipmentById', 'getDefaultEquipment', 'getDefaultEquipmentById', 'getOtherEquipment', 'getOtherEquipmentById', 'addEquipment', 'postAddEquipment', 'editEquipment', 'postEditEquipment', 'deleteEquipment', 'postDeleteEquipment', 'postAjaxFetchEquipmentById', 'getIndex']]);
	}


	/* Get Equipment */

	public function getEquipment(){

		// return 404 if not logged in
		if(empty(Session::has('id')))
			return View::make('404');


		$query = Equipment::user(Session::get('id'));

		if(Input::has('type')){
			if(Input::get('type') == 'default')
				$query->isDefault();
			elseif(Input::get('type') == 'others')
				$query->isOthers();
		}

		$equipment = $query->get();

		return View::make('ourscene.equipment')->with('equipment', $equipment);

	}

	public function getAjaxEquipment(){
		$query = Equipment::user(Session::get('id'));

		if(Input::has('type')){
			if(Input::get('type') == 'default')
				$query->isDefault();
			elseif(Input::get('type') == 'others')
				$query->isOthers();
		}

		return Response::json($query->get());
	}

	/* Add Equipment */

	public function addEquipment(){

		if(empty(Session::has('id')))
			return View::make('404');

		return View::make('ourscene.profile.add-equipment');

	}

	// AJAX request for adding equipment
	public function postAjaxAddEquipment(){
		$response = ['error' => true];
		$data = Input::all();

		if(Session::has('id')){

			$user = User::find(Session::get('id'));

			$equipment = new Equipment;

			$equipment->user_id = $user->id;
			$equipment->owner = $user->name;

			if(isset($data['default']) && count($data['default']) > 0)
				$equipment->type = 'default';
			else
				$equipment->type = 'others';

			if($data['equipment_name'] != '')
				$equipment->name = $data['equipment_name'];

			$contents = [];
			foreach ($data['contents'] as $content) {
				array_push($contents, $content);
			}

			$equipment->inclusion = $contents;
			$equipment->save();

			return Redirect::to('/settings#equipment')->with('success-equipment', 'The equipment was added.');
		}

	}

	public function postAjaxUpdateEquipment(){
		$response = ['error' => true];
		$data = Input::all();

		if(Session::has('id')){

			$user = User::find(Session::get('id'));

			$equipment = new Equipment;

			$equipment->user_id = $user->id;
			$equipment->owner = $user->name;

			if(isset($data['default']) && count($data['default']) > 0)
				$equipment->type = 'default';
			else
				$equipment->type = 'others';

			if($data['equipment_name'] != '')
				$equipment->name = $data['equipment_name'];

			$contents = [];
			foreach ($data['contents'] as $content) {
				array_push($contents, $content);
			}

			$equipment->inclusion = $contents;
			$equipment->save();

			return Redirect::to('/settings#equipment')->with('success-equipment', 'The equipment was added.');
		}

	}

	/* Edit Equipment */

	public function editEquipment($id){

		$equipment = Equipment::find($id);
		$contents = $equipment->inclusion;

		if(empty(Session::has('id')))
			return View::make('404');

		Session::put('equipment_id', $id);
		Session::put('contents', $contents);

		return View::make('ourscene.edit-equipment')->with('equipment', $equipment)->with('contents', $contents);

	}

	public function postEditEquipment(){

		$equipment = Equipment::find(Session::get('equipment_id'));

		if($equipment){

			//trim and sanitize all inputs
			Input::merge(array_map('trim', Input::all()));
			$input = filter_var_array(Input::all(), FILTER_SANITIZE_STRIPPED);

			if(Input::get('default') === 'yes')
				$equipment->type = 'default';
			else
				$equipment->type = 'others';

			if(Input::get('equipment_name') != '')
				$equipment->name = Input::get('equipment_name');

			if(Input::get('qty') != '')
				$equipment->qty = Input::get('qty');

			$contents = array();

			for($i=1; $i<=5; $i++){
				if(Input::get('content_'.$i, '') != '')
					array_push($contents, $input['content_'.$i]);
			}

			$equipment->inclusion = $contents;
			$equipment->save();

		}

		return Redirect::to('/equipment')->with('success', 'You have successfully edited an equipment.');

	}

	/* Delete Equipment */

	public function deleteEquipment($id){

		$equipment = Equipment::find($id);

		if(empty(Session::has('id')))
			return View::make('404');

		Session::put('equipment_id', $id);

		return View::make('ourscene.delete-equipment')->with('equipment', $equipment);

	}

	public function postDeleteEquipment(){

		$equipment = Equipment::find(Session::get('equipment_id'));

		$action = Input::get('delete_equipment', 'none');

		if(!is_null($equipment)){
			if($action === 'yes'){
				$equipment->delete();
				return Redirect::to('/equipment')->with('error', 'You have deleted an equipment.');
			}
		}

		return Redirect::to('/equipment');

	}

	public function postAjaxFetchEquipmentById(){
		$id = Input::get('id');

		return Equipment::find($id);
	}

	public function array_map_recursive($callback, $array){
		foreach($array as $key => $value) {
			if(is_array($array[$key]))
				$array[$key] = $this->array_map_recursive($callback, $array[$key]);
			else
				$array[$key] = call_user_func($callback, $array[$key]);
		}

		return $array;
	}

}
