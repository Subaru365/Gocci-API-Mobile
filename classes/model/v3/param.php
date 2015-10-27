<?php
/**
 * Parameter list of uri.
 *
 * @package    Gocci-Mobile
 * @version    3.0 (2015/10/27)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @license    MIT License
 * @copyright  2015 Inase,inc.
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

class Model_V3_Param extends Model
{
	/**
	 * @var $uri
	 */
	private $uri = '';

	/**
	 * @var Array $input_data
	 */
	private $input_data = array();

	/**
	 * @var Array $form_data
	 */
	private $format_data = array();

	/** 
	 * @var Instance $val 
	 */
	private $Val;


	public function __construct()
	{
		$this->uri = Uri::string();
		$this->Val = Validation::forge();
	}


	//======================================================//
	// Request
	//======================================================//

	public function get_request()
	{
		$this->set_request();
		$this->check();
		return $this->format_data;
	}

	//-----------------------------------------------------//

	private function set_request()
	{
		switch ($this->uri) {

			case 'v3/auth/signup':
				$this->get_req_signup();
				$this->set_req_signup();
				break;

			case 'v3/auth/login':
				$this->get_req_login();
				$this->set_req_login();
				break;

			case 'v3/auth/sns_login':
				$this->get_req_sns_login();
				$this->set_req_sns_login();
				break;

			case 'v3/auth/pass_login':
				$this->get_req_pass_login();
				$this->set_req_pass_login();
				break;

			default:
				Model_V3_Status::ERROR_CONNECTION_FAILED();
				break;
		}
	}


	// Request Params
	//-----------------------------------------------------//

	private function get_req_signup()
	{
		$this->input_data = array(
			'username' 	  => Input::get('username'),
			'os' 		  => Input::get('os'),
			'ver'		  => Input::get('ver'),
			'model' 	  => Input::get('model'),
			'register_id' => Input::get('register_id')
		);
	}

	private function get_req_login()
	{
		$this->input_data = array(
			'identity_id' => Input::get('identity_id')
		);
	}

	private function get_req_sns_login()
	{
		$this->input_data = array(
			'identity_id' => Input::get('identity_id'),
			'os' 		  => Input::get('os'),
			'ver' 		  => Input::get('ver'),
			'model' 	  => Input::get('model'),
			'register_id' => Input::get('register_id')
		);
	}

	private function get_req_pass_login()
	{
		$this->input_data = array(
			'username' 	  => Input::get('username'),
			'password' 	  => Input::get('password'),
			'register_id' => Input::get('register_id')
		);
	}

	private function set_req_signup()
	{
		$this->regex_username();
		$this->regex_os();
		$this->regex_ver();
		$this->regex_model();
		$this->regex_register_id();
	}

	private function set_req_login()
	{
		$this->regex_identity_id();
	}

	private function set_req_sns_login()
	{
		$this->regex_identity_id();
		$this->regex_os();
		$this->regex_ver();
		$this->regex_model();
		$this->regex_register_id();
	}

	private function set_req_pass_login()
	{
		$this->regex_username();
		$this->regex_password();
		$this->regex_register_id();
	}


	//======================================================//
	// Responce Params
	//======================================================//

	public function get_responce()
	{
		$this->set_responce();
		$this->check();
		return $this->format_data;
	}

	//-----------------------------------------------------//

	private function set_responce()
	{
		switch ($this->uri) {

			case 'v3/auth/signup':
				$this->get_res_signup();
				$this->set_res_signup();
				break;

			case 'v3/auth/login':
				$this->get_res_login();
				$this->set_res_login();
				break;

			case 'v3/auth/sns_login':
				$this->get_res_sns_login();
				$this->set_res_sns_login();
				break;

			case 'v3/auth/pass_login':
				$this->get_res_pass_login();
				$this->set_res_pass_login();
				break;

			default:
				Model_V3_Status::ERROR_CONNECTION_FAILED();
				break;
		}
	}


	// Request Params
	//-----------------------------------------------------//

	private function get_res_signup()
	{
		$this->input_data = array(
			'username' 	  => Input::get('username'),
			'os' 		  => Input::get('os'),
			'ver'		  => Input::get('ver'),
			'model' 	  => Input::get('model'),
			'register_id' => Input::get('register_id')
		);
	}

	private function get_res_login()
	{
		$this->input_data = array(
			'identity_id' => Input::get('identity_id')
		);
	}

	private function get_res_sns_login()
	{
		$this->input_data = array(
			'identity_id' => Input::get('identity_id'),
			'os' 		  => Input::get('os'),
			'ver' 		  => Input::get('ver'),
			'model' 	  => Input::get('model'),
			'register_id' => Input::get('register_id')
		);
	}

	private function get_res_pass_login()
	{
		$this->input_data = array(
			'username' 	  => Input::get('username'),
			'password' 	  => Input::get('password'),
			'register_id' => Input::get('register_id')
		);
	}


	//======================================================//
	// RegEx methods
	//======================================================//

	private function regex_username()
	{
		$this->Val
		->add('username', 'GET username')
		->add_rule('required')
		->add_rule('match_pattern', '/^\w{4,20}$/');
	}

	private function regex_os()
	{
		$this->Val
		->add('os', 'GET os')
		->add_rule('required')
		->add_rule('match_pattern', '/^android$|^iOS$/');
	}

	private function regex_ver()
	{
		$this->Val
		->add('ver', 'GET ver')
		->add_rule('required')
		->add_rule('match_pattern', '/^[0-9]+$/');
	}

	private function regex_model()
	{
		$this->Val
		->add('model', 'GET model')
		->add_rule('required')
		->add_rule('match_pattern', '/^[a-zA-Z0-9_-]{0,10}$/');
	}

	private function regex_password()
	{
		$this->Val
		->add('pass', 'GET password')
		->add_rule('required')
		->add_rule('match_pattern', '/^\w{6,25}$/');
	}

	private function regex_identity_id()
	{
		$this->Val
		->add('identity_id', 'GET identity_id')
		->add_rule('required')
		->add_rule('match_pattern', '/^us-east-1:[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/');
	}

	private function regex_register_id()
	{
		$this->Val
		->add('register_id', 'GET register_id')
		->add_rule('required')
		->add_rule('match_pattern', '/^([a-f0-9]{64})|([a-zA-Z0-9:_-]{140,250})$/');
	}

	//======================================================//

	//Valodation Check
	private function check()
	{
		$val  = $this->Val;
		$data = $this->input_data;

		if($val->run($data)){
		    //OK
		    $this->format_data = $data;

		}else{
			//エラー 形式不備
		    foreach($val->error() as $key=>$value){
		    	$keys[]		= $key;
		    	$messages[] = $value;
		    }

		    $key 		= implode(", ", $keys);
		    $message    = implode(". ", $messages);

		    Controller_V2_Mobile_Base::output_validation_error($key, $message);
		    error_log("$message");
		}
	}
}