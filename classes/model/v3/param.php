<?php
/**
 * Parameter list of uri.
 *
 * @package    Gocci-Mobile
 * @version    3.0 (2015/11/06)
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
	 * @var Array $val_param
	 */
	private $val_param = array();

	/**
	 * @var Array $safe_param //if validation failed return FALSE
	 */
	private $safe_param = array();

	/**
	 * @var Array $safe_param //if validation failed return FALSE
	 */
	private $res_param = array();


	/**
	 * @var Instance $Val
	 */
	private $Val;




	public function __construct()
	{
		$this->uri = Uri::string();
	}


	//======================================================//
	// Request
	//======================================================//

	public function get_request()
	{
		$this->Val = Validation::forge('request');

		$this->setRequest();
		$this->check($this->val_param);

		return $this->safe_param;
	}

	//-----------------------------------------------------//

	private function setRequest()
	{
		switch ($this->uri) {

			case 'v3/auth/login':
				$this->getReq_login();
				$this->setReq_login();
				break;

			case 'v3/auth/check':
				$this->getReq_check();
				$this->setReq_check();
				break;

			case 'v3/auth/signup':
				$this->getReq_signup();
				$this->setReq_signup();
				break;

			case 'v3/auth/sns_login':
				$this->getReq_sns_login();
				$this->setReq_sns_login();
				break;

			case 'v3/auth/pass_login':
				$this->getReq_pass_login();
				$this->setReq_pass_login();
				break;

			case 'v3/get/nearline':
				$this->getReq_nearline();
				$this->setReq_nearline();
				break;

			case 'v3/get/followline':
				$this->getReq_followline();
				$this->setReq_followline();
				break;

			case 'v3/get/timeline':
				$this->getReq_timeline();
				$this->setReq_timeline();
				break;

			case 'v3/get/user':
				$this->getReq_user();
				$this->setReq_user();
				break;


			case 'v3/get/heatmap':
				break;

			default:
				Model_V3_Status::get_status();
				break;
		}
	}


	// Request Params
	//-----------------------------------------------------//

	private function getReq_login()
	{
		$this->val_param = array(
			'identity_id' => Input::get('identity_id'),
		);
	}

	private function getReq_check()
	{
		$this->val_param = array(
			'register_id' => Input::get('register_id'),
		);
	}

	private function getReq_signup()
	{
		$this->val_param = array(
			'username' 	  => Input::get('username'),
			'os' 		  => Input::get('os'),
			'ver'		  => Input::get('ver'),
			'model' 	  => Input::get('model'),
			'register_id' => Input::get('register_id'),
		);
	}

	private function getReq_sns_login()
	{
		$this->val_param = array(
			'identity_id' => Input::get('identity_id'),
			'os' 		  => Input::get('os'),
			'ver' 		  => Input::get('ver'),
			'model' 	  => Input::get('model'),
			'register_id' => Input::get('register_id'),
		);
	}

	private function getReq_pass_login()
	{
		$this->val_param = array(
			'username' 	  => Input::get('username'),
			'password'    => Input::get('password'),
			'os' 		  => Input::get('os'),
			'ver' 		  => Input::get('ver'),
			'model' 	  => Input::get('model'),
			'register_id' => Input::get('register_id'),
		);
	}

	private function getReq_nearline()
	{
		$this->val_param = array(
			'lon' 	     	=> Input::get('lon'),
			'lat' 	     	=> Input::get('lat'),
			'category_id'	=> Input::get('category_id'),
			'valuse_id'  	=> Input::get('valuse_id'),
			'page' 	     	=> Input::get('page'),
		);
	}

	private function getReq_followline()
	{
		$this->val_param = array(
			'category_id'	=> Input::get('category_id'),
			'valuse_id'  	=> Input::get('valuse_id'),
			'page' 	     	=> Input::get('page'),
		);
	}

	private function getReq_timeline()
	{
		$this->val_param = array(
			'category_id'	=> Input::get('category_id'),
			'valuse_id'  	=> Input::get('valuse_id'),
			'page' 	     	=> Input::get('page'),
		);
	}

	private function getReq_user()
	{
		$this->val_param = array(
			'user_id' 		=> Input::get('user_id'),
		);
	}



	private function setReq_login()
	{
		$this->regex_identity_id();
	}

	private function setReq_check()
	{
		$this->regex_register_id();
	}

	private function setReq_signup()
	{
		$this->regex_username();
		$this->regex_os();
		$this->regex_ver();
		$this->regex_model();
		$this->regex_register_id();
	}

	private function setReq_sns_login()
	{
		$this->regex_identity_id();
		$this->regex_os();
		$this->regex_ver();
		$this->regex_model();
		$this->regex_register_id();
	}

	private function setReq_pass_login()
	{
		$this->regex_username();
		$this->regex_password();
		$this->regex_register_id();
	}

	private function setReq_nearline()
	{
		$this->regex_lat();
		$this->regex_lon();
		$this->regex_category_id();
		$this->regex_value_id();
		$this->regex_page();
	}

	private function setReq_followline()
	{
		$this->regex_category_id();
		$this->regex_value_id();
		$this->regex_page();
	}

	private function setReq_timeline()
	{
		$this->regex_category_id();
		$this->regex_value_id();
		$this->regex_page();
	}

	private function setReq_user()
	{
		$this->regex_user_id();
	}



	//======================================================//
	// Responce Params
	//======================================================//

	public function get_responce($param)
	{
		$this->Val = Validation::forge('responce');
		$this->val_param = $param;
		$this->set_responce();
		$this->check($param);
		return $this->safe_param;
	}

	//-----------------------------------------------------//

	private function set_responce()
	{
		switch ($this->uri) {

			case 'v3/auth/login':
				$this->set_res_login();
				break;

			case 'v3/auth/check':
				break;

			case 'v3/auth/signup':
				$this->set_res_signup();
				break;

			case 'v3/auth/sns_login':
				$this->set_res_sns_login();
				break;

			case 'v3/auth/pass_login':
				$this->set_res_pass_login();
				break;

			case '/v3/get/nearline':
				break;

			case 'v3/get/heatmap':
				break;


			default:
				//Model_V3_Status::ERROR_CONNECTION_FAILED();
				break;
		}
	}


	// Responce Params
	//-----------------------------------------------------//

	// private function get_res_signup()
	// {
	// 	$data = $this->safe_param;
	// 	$res_param['user_id']		= $data['user_id'];
	// 	$res_param['username']		= $data['username'];
	// 	$res_param['profile_img']	= $data['profile_img'];
	// 	$res_param['identity_id']	= $data['identity_id'];
	// 	$res_param['badge_num']		= $data['badge_num'];
	// 	$res_param['cognito_token']	= $data['cognito_token'];
	// 	$this->res_param = $res_param;
	// }

	// private function get_res_login()
	// {
	// 	$data = $this->safe_param;
	// 	$res_param['user_id']		= $data['user_id'];
	// 	$res_param['username']		= $data['username'];
	// 	$res_param['profile_img']	= $data['profile_img'];
	// 	$res_param['identity_id']	= $data['identity_id'];
	// 	$res_param['badge_num']		= $data['badge_num'];
	// 	$res_param['cognito_token']	= $data['cognito_token'];
	// 	$this->res_param = $res_param;
	// }

	// private function get_res_sns_login()
	// {
	// 	$data = $this->safe_param;
	// 	$res_param['user_id']		= $data['user_id'];
	// 	$res_param['username']		= $data['username'];
	// 	$res_param['profile_img']	= $data['profile_img'];
	// 	$res_param['identity_id']	= $data['identity_id'];
	// 	$res_param['badge_num']		= $data['badge_num'];
	// 	$res_param['cognito_token']	= $data['cognito_token'];
	// 	$this->res_param = $res_param;
	// }

	// private function get_res_pass_login()
	// {
	// 	$data = $this->safe_param;
	// 	$res_param['user_id']		= $data['user_id'];
	// 	$res_param['username']		= $data['username'];
	// 	$res_param['profile_img']	= $data['profile_img'];
	// 	$res_param['identity_id']	= $data['identity_id'];
	// 	$res_param['badge_num']		= $data['badge_num'];
	// 	$res_param['cognito_token']	= $data['cognito_token'];
	// 	$this->res_param = $res_param;
	// }


	private function set_res_signup()
	{
		$this->regex_user_id();
		$this->regex_username();
		$this->regex_profile_img();
		$this->regex_identity_id();
		$this->regex_badge_num();
		$this->regex_cognito_token();
	}

	private function set_res_login()
	{
		$this->regex_user_id();
		$this->regex_username();
		$this->regex_profile_img();
		$this->regex_identity_id();
		$this->regex_badge_num();
		$this->regex_cognito_token();
	}

	private function set_res_sns_login()
	{
		$this->regex_user_id();
		$this->regex_username();
		$this->regex_profile_img();
		$this->regex_identity_id();
		$this->regex_badge_num();
		$this->regex_cognito_token();
	}

	private function set_res_pass_login()
	{
		$this->regex_user_id();
		$this->regex_username();
		$this->regex_profile_img();
		$this->regex_identity_id();
		$this->regex_badge_num();
		$this->regex_cognito_token();
	}

	//======================================================//
	// RegEx methods
	//======================================================//

	private function regex_user_id()
	{
		$this->Val
		->add('user_id', 'GET user_id')
		->add_rule('required')
		->add_rule('match_pattern', '/^[0-9]+$/');
	}

	private function regex_username()
	{
		$this->Val
		->add('username', 'GET username')
		->add_rule('required')
		->add_rule('match_pattern', '/^\S{4,20}$/');
		# /^\S{3,15}$/
	}

	private function regex_password()
	{
		$this->Val
		->add('password', 'GET password')
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

	private function regex_profile_img()
	{
		$this->Val
		->add('profile_img', 'GET profile_img')
		->add_rule('required')
		->add_rule('match_pattern', '/^http\S+$/');
	}

	private function regex_badge_num()
	{
		$this->Val
		->add('badge_num', 'GET badge_num')
		->add_rule('required')
		->add_rule('match_pattern', '/^[0-9]+$/');
	}

	private function regex_cognito_token()
	{
		$this->Val
		->add('cognito_token', 'GET cognito_token')
		->add_rule('required')
		->add_rule('match_pattern', '/^[a-zA-Z0-9_.-]{400,2200}$/');
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
		->add_rule('match_pattern', '/^\d+\.\d+$/');
	}

	private function regex_model()
	{
		$this->Val
		->add('model', 'GET model')
		->add_rule('required')
		->add_rule('match_pattern', '/^[a-zA-Z0-9_-]{0,10}$/');
	}

	private function regex_register_id()
	{
		$this->Val
		->add('register_id', 'GET register_id')
		->add_rule('required')
		->add_rule('match_pattern', '/^([a-f0-9]{64})|([a-zA-Z0-9:_-]{140,250})$/');
	}

	private function regex_lon()
	{
		$this->Val
		->add('lon', 'GET lon')
		->add_rule('required')
		->add_rule('match_pattern', '/^[0-9.]+$/');
	}

	private function regex_lat()
	{
		$this->Val
		->add('lat', 'GET lat')
		->add_rule('required')
		->add_rule('match_pattern', '/^[0-9.]+$/');
	}

	private function regex_category_id()
	{
		$this->Val
		->add('category_id', 'GET category_id')
		->add_rule('match_pattern', '/^[0-9]$/');
	}

	private function regex_value_id()
	{
		$this->Val
		->add('value_id', 'GET value_id')
		->add_rule('match_pattern', '/^[0-9]$/');
	}

	private function regex_page()
	{
		$this->Val
		->add('page', 'GET page')
		->add_rule('match_pattern', '/^[0-9]$/');
	}

	//======================================================//

	//Valodation Check
	private function check($val_param)
	{
		$Val = $this->Val;

		if($Val->run($val_param)){
		    //OK
		    $this->safe_param = $val_param;

		}else{
			//エラー 形式不備
		    foreach($Val->error() as $key=>$value){
		    	$keys[]		= $key;
		    	$messages[] = $value;
		    }

		    $key 		= implode(", ", $keys);
		    $message    = implode(". ", $messages);

		    error_log("$message");
		    $this->safe_param = FALSE;
		}
	}
}