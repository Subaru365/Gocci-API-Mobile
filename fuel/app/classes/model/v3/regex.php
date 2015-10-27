<?php
/**
 * Check at input data of uri.
 *
 * @package    Gocci-Mobile
 * @version    3.0 (2015/10/26)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @license    MIT License
 * @copyright  2015 Inase,inc.
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

class Model_V3_Regex extends Model
{

	/** 
	 * @var Array $data 
	 */
	private $verify_data = array();

	/** 
	 * @var Instance $val 
	 */
	private $val;


	//-----------------------------------------------------//


	public function __construct()
	{
		$this->val = Validation::forge();
	}


	public function check_request($data)
	{
		$this->verify_data = $data;
		$this->set_request();
		$this->run();
	}


	private function set_request($data)
	{
		switch (Uri::string()) {

			case 'v3/auth/signup':
				$this->set_req_signup();
				break;

			case 'v3/auth/login':
				$this->set_req_login();
				break;

			case 'v3/auth/sns_login':
				$this->set_req_sns_login();
				break;

			case 'v3/auth/pass_login':
				$this->set_req_pass_login();
				break;

			default:
				Model_V3_Status::ERROR_CONNECTION_FAILED();
				break;
		}
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
	// Set RegEx
	//======================================================//

	public function check_response()
	{
		$this->verify_data = $data;
		$this->set_responce();
		$this->run();
	}


	public function set_responce()
	{
		switch (Uri::string()) {

			case 'v3/auth/signup':
				$this->set_regex_signup();
				break;

			case 'v3/auth/login':
				$this->set_regex_login();
				break;

			case 'v3/auth/sns_login':
				$this->set_regex_sns_login();
				break;

			case 'v3/auth/pass_login':
				$this->set_regex_pass_login();
				break;

			default:
				Model_V3_Status::ERROR_CONNECTION_FAILED();
				break;
		}
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
	// RegEx methods
	//======================================================//

	private function regex_username()
	{
		$this->val
		->add('username', 'GET username')
		->add_rule('required')
		->add_rule('match_pattern', '/^\w{4,20}$/');
	}

	private function regex_os()
	{
		$this->val
		->add('os', 'GET os')
		->add_rule('required')
		->add_rule('match_pattern', '/^android$|^iOS$/');
	}

	private function regex_ver()
	{
		$this->val
		->add('ver', 'GET ver')
		->add_rule('required')
		->add_rule('match_pattern', '/^[0-9]+$/');
	}

	private function regex_model()
	{
		$this->val
		->add('model', 'GET model')
		->add_rule('required')
		->add_rule('match_pattern', '/^[a-zA-Z0-9_-]{0,10}$/');
	}

	private function regex_password()
	{
		$this->val
		->add('pass', 'GET password')
		->add_rule('required')
		->add_rule('match_pattern', '/^\w{6,25}$/');
	}

	private function regex_identity_id()
	{
		$this->val
		->add('identity_id', 'GET identity_id')
		->add_rule('required')
		->add_rule('match_pattern', '/^us-east-1:[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/');
	}

	private function regex_register_id()
	{
		$this->val
		->add('register_id', 'GET register_id')
		->add_rule('required')
		->add_rule('match_pattern', '/^([a-f0-9]{64})|([a-zA-Z0-9:_-]{140,250})$/');
	}

	//======================================================//

	//Valodation Check Run
	private function run()
	{
		$val  = $this->val;
		$data = $this->verify_data;

		if($val->run($data)){
		    //OK
			return true;

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

		    return false;
		}
	}
}