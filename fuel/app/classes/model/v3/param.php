<?php
/**
 * Parameter list of uri.
 *
 * @package    Gocci-Mobile
 * @version    3.0.0 (2015/12/09)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @copyright  (C) 2015 Akira Murata
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

class Model_V3_Param extends Model
{
	use SingletonTrait;

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
	 * @var Instance $val
	 */
	private $val;

	private function __construct()
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

		if ($this->val_param) {
			$this->check($this->val_param);

		} else {
			return true;
		}

		return $this->safe_param;
	}

	//-----------------------------------------------------//

	private function setRequest()
	{
		switch ($this->uri) {

			case 'v3/auth/login':
				$this->getReq_auth_login();
				$this->setReq_auth_login();
				break;

			case 'v3/auth/signup':
				$this->getReq_auth_signup();
				$this->setReq_auth_signup();
				break;

			case 'v3/auth/password':
				$this->getReq_auth_password();
				$this->setReq_auth_password();
				break;

			case 'v3/get/nearline':
				$this->getReq_get_nearline();
				$this->setReq_get_nearline();
				break;

			case 'v3/get/followline':
				$this->getReq_get_followline();
				$this->setReq_get_followline();
				break;

			case 'v3/get/timeline':
				$this->getReq_get_timeline();
				$this->setReq_get_timeline();
				break;

			case 'v3/get/user':
				$this->getReq_get_user();
				$this->setReq_get_user();
				break;

			case 'v3/get/rest':
				$this->getReq_get_rest();
				$this->setReq_get_rest();
				break;

			case 'v3/get/comment':
				$this->getReq_get_comment();
				$this->setReq_get_comment();
				break;

			case 'v3/get/heatmap':
				break;

			case 'v3/get/notice':
				break;

			case 'v3/get/follow':
				$this->getReq_get_follow();
				$this->setReq_get_follow();
				break;

			case 'v3/get/follower':
				$this->getReq_get_follower();
				$this->setReq_get_follower();
				break;

			case 'v3/get/want':
				$this->getReq_get_want();
				$this->setReq_get_want();
				break;

			case 'v3/get/user_cheer':
				$this->getReq_get_user_cheer();
				$this->setReq_get_user_cheer();
				break;

			case 'v3/get/near':
				$this->getReq_get_near();
				$this->setReq_get_near();
				break;

			case 'v3/set/password':
				$this->getReq_set_password();
				$this->setReq_set_password();
				break;

			case 'v3/set/device':
				$this->getReq_set_device();
				$this->setReq_set_device();
				break;

			case 'v3/set/sns_link':
				$this->getReq_set_sns_link();
				$this->setReq_set_sns_link();
				break;

			case 'v3/set/gochi':
				$this->getReq_set_gochi();
				$this->setReq_set_gochi();
				break;

			case 'v3/set/comment':
				$this->getReq_set_comment();
				$this->setReq_set_comment();
				break;

			case 'v3/set/follow':
				$this->getReq_set_follow();
				$this->setReq_set_follow();
				break;

			case 'v3/set/want':
				$this->getReq_set_want();
				$this->setReq_set_want();
				break;

			case 'v3/set/post':
				$this->getReq_set_post();
				$this->setReq_set_post();
				break;

			case 'v3/set/post_block':
				$this->getReq_set_post_block();
				$this->setReq_set_post_block();
				break;

			case 'v3/set/username':
				$this->getReq_set_username();
				$this->setReq_set_username();
				break;

			case 'v3/set/profile_img':
				$this->getReq_set_profile_img();
				$this->setReq_set_profile_img();
				break;

			case 'v3/set/feedback':
				$this->getReq_set_feedback();
				$this->setReq_set_feedback();
				break;

			case 'v3/set/rest':
				$this->getReq_set_rest();
				$this->setReq_set_rest();
				break;

			case 'v3/unset/sns_link':
				$this->getReq_unset_sns_link();
				$this->setReq_unset_sns_link();
				break;

			case 'v3/unset/post':
				$this->getReq_unset_post();
				$this->setReq_unset_post();
				break;

			case 'v3/unset/follow':
				$this->getReq_unset_follow();
				$this->setReq_unset_follow();
				break;

			case 'v3/unset/want':
				$this->getReq_unset_want();
				$this->setReq_unset_want();
				break;

			default:
				Model_V3_Status::getStatus();
				break;
		}
	}


	// Get Request Params
	//-----------------------------------------------------//

	private function getReq_auth_login()
	{
		$this->val_param = array(
			'identity_id' 	=> Input::get('identity_id'),
		);
	}

	private function getReq_auth_signup()
	{
		$this->val_param = array(
			'username' 	  	=> Input::get('username'),
		);
	}

	private function getReq_auth_password()
	{
		$this->val_param = array(
			'username' 	  	=> Input::get('username'),
			'password'    	=> Input::get('password'),
		);
	}

	private function getReq_get_nearline()
	{
		$this->val_param = array(
			'lon' 	     	=> Input::get('lon'),
			'lat' 	     	=> Input::get('lat'),
			'category_id'	=> Input::get('category_id'),
			'value_id'  	=> Input::get('value_id'),
			'page' 	     	=> Input::get('page'),
		);
	}

	private function getReq_get_followline()
	{
		$this->val_param = array(
			'category_id'	=> Input::get('category_id'),
			'value_id'  	=> Input::get('value_id'),
			'page' 	     	=> Input::get('page'),
		);
	}

	private function getReq_get_timeline()
	{
		$this->val_param = array(
			'category_id'	=> Input::get('category_id'),
			'value_id'  	=> Input::get('value_id'),
			'page' 	     	=> Input::get('page'),
		);
	}

	private function getReq_get_user()
	{
		$this->val_param = array(
			'user_id' 		=> Input::get('user_id'),
		);
	}

	private function getReq_get_rest()
	{
		$this->val_param = array(
			'rest_id' 		=> Input::get('rest_id'),
		);
	}

	private function getReq_get_comment()
	{
		$this->val_param = array(
			'post_id'		=> Input::get('post_id'),
		);
	}

	private function getReq_get_follow()
	{
		$this->val_param = array(
			'user_id'		=> Input::get('user_id'),
		);
	}

	private function getReq_get_follower()
	{
		$this->val_param = array(
			'user_id'		=> Input::get('user_id'),
		);
	}

	private function getReq_get_want()
	{
		$this->val_param = array(
			'user_id'		=> Input::get('user_id'),
		);
	}

	private function getReq_get_user_cheer()
	{
		$this->val_param = array(
			'user_id' 		=> Input::get('user_id'),
		);
	}

	private function getReq_get_near()
	{
		$this->val_param = array(
			'lon' 			=> Input::get('lon'),
			'lat' 			=> Input::get('lat'),
		);
	}

	private function getReq_set_device()
	{
		$this->val_param = array(
			'os'			=> Input::get('os'),
			'ver' 			=> Input::get('ver'),
			'model'			=> Input::get('model'),
			'device_token' 	=> Input::get('device_token'),
		);
	}

	private function getReq_set_password()
	{
		$this->val_param = array(
			'password' 		=> Input::get('password'),
		);
	}

	private function getReq_set_sns_link()
	{
		$this->val_param = array(
			'provider' 		=> Input::get('provider'),
			'sns_token' 	=> Input::get('sns_token'),
		);
	}

	private function getReq_unset_sns_link()
	{
		$this->val_param = array(
			'provider' 		=> Input::get('provider'),
			'sns_token' 	=> Input::get('sns_token'),
		);
	}

	private function getReq_set_gochi()
	{
		$this->val_param = array(
			'post_id' 		=> Input::get('post_id'),
		);
	}

	private function getReq_set_comment()
	{
		$this->val_param = array(
			'post_id' 		=> Input::get('post_id'),
			'comment' 		=> Input::get('comment'),
			're_user_id' 	=> Input::get('re_user_id'),
		);
	}

	private function getReq_set_follow()
	{
		$this->val_param = array(
			'user_id' 		=> Input::get('user_id'),
		);
	}

	private function getReq_unset_follow()
	{
		$this->val_param = array(
			'user_id' 		=> Input::get('user_id'),
		);
	}

	private function getReq_set_want()
	{
		$this->val_param = array(
			'rest_id' 		=> Input::get('rest_id'),
		);
	}

	private function getReq_unset_want()
	{
		$this->val_param = array(
			'rest_id' 		=> Input::get('rest_id'),
		);
	}

	private function getReq_set_post()
	{
		$this->val_param = array(
			'rest_id' 		=> Input::get('rest_id'),
			'movie_name' 	=> Input::get('movie_name'),
			'category_id' 	=> Input::get('category_id'),
			'value' 		=> Input::get('value'),
			'memo' 			=> Input::get('memo'),
			'cheer_flag' 	=> Input::get('cheer_flag'),
		);
	}

	private function getReq_set_post_block()
	{
		$this->val_param = array(
			'post_id' 		=> Input::get('post_id'),
		);
	}

	private function getReq_unset_post()
	{
		$this->val_param = array(
			'post_id' 		=> Input::get('post_id'),
		);
	}

	private function getReq_set_username()
	{
		$this->val_param = array(
			'username' 		=> Input::get('username'),
		);
	}

	private function getReq_set_profile_img()
	{
		$this->val_param = array(
			'profile_img' 	=> Input::get('profile_img'),
		);
	}

	private function getReq_set_feedback()
	{
		$this->val_param = array(
			'feedback' 		=> Input::get('feedback'),
		);
	}

	private function getReq_set_rest()
	{
		$this->val_param = array(
			'restname' 		=> Input::get('restname'),
			'lon'	 		=> Input::get('lon'),
			'lat' 			=> Input::get('lat'),
		);
	}



	// Set Regex Request Params
	//-----------------------------------------------------//

	private function setReq_auth_login()
	{
		$this->regex_identity_id();
	}

	private function setReq_auth_signup()
	{
		$this->regex_username();
	}

	private function setReq_auth_password()
	{
		$this->regex_username();
		$this->regex_password();
	}

	private function setReq_get_nearline()
	{
		$this->regex_lat();
		$this->regex_lon();
		$this->regex_category_id();
		$this->regex_value_id();
		$this->regex_page();
	}

	private function setReq_get_followline()
	{
		$this->regex_category_id();
		$this->regex_value_id();
		$this->regex_page();
	}

	private function setReq_get_timeline()
	{
		$this->regex_category_id();
		$this->regex_value_id();
		$this->regex_page();
	}

	private function setReq_get_user()
	{
		$this->regex_user_id();
	}

	private function setReq_get_rest()
	{
		$this->regex_rest_id();
	}

	private function setReq_get_comment()
	{
		$this->regex_post_id();
	}

	private function setReq_get_follow()
	{
		$this->regex_user_id();
	}

	private function setReq_get_follower()
	{
		$this->regex_user_id();
	}

	private function setReq_get_want()
	{
		$this->regex_user_id();
	}

	private function setReq_get_user_cheer()
	{
		$this->regex_user_id();
	}

	private function setReq_get_near()
	{
		$this->regex_lon();
		$this->regex_lat();
	}

	private function setReq_set_device()
	{
		$this->regex_os();
		$this->regex_ver();
		$this->regex_model();
		$this->regex_device_token();
	}

	private function setReq_set_password()
	{
		$this->regex_password();
	}


	private function setReq_set_sns_link()
	{
		$this->regex_provider();
		$this->regex_sns_token();
	}

	private function setReq_unset_sns_link()
	{
		$this->regex_provider();
		$this->regex_sns_token();
	}

	private function setReq_set_gochi()
	{
		$this->regex_post_id();
	}

	private function setReq_set_comment()
	{
		$this->regex_post_id();
		$this->regex_comment();
		$this->regex_re_user_id();
	}

	private function setReq_set_follow()
	{
		$this->regex_user_id();
	}

	private function setReq_unset_follow()
	{
		$this->regex_user_id();
	}

	private function setReq_set_want()
	{
		$this->regex_rest_id();
	}

	private function setReq_unset_want()
	{
		$this->regex_rest_id();
	}

	private function setReq_set_post()
	{
		$this->regex_rest_id();
		$this->regex_movie_name();
		$this->regex_category_id();
		$this->regex_value();
		$this->regex_memo();
		$this->regex_cheer_flag();
	}

	private function setReq_set_post_block()
	{
		$this->regex_post_id();
	}

	private function setReq_unset_post()
	{
		$this->regex_post_id();
	}

	private function setReq_set_username()
	{
		$this->regex_username();
	}

	private function setReq_set_profile_img()
	{
		$this->regex_profile_img();
	}

	private function setReq_set_feedback()
	{
		$this->regex_feedback();
	}

	private function setReq_set_rest()
	{
		$this->regex_restname();
		$this->regex_lon();
		$this->regex_lat();
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

	private function regex_re_user_id()
	{
		$this->Val
		->add('re_user_id', 'GET re_user_id')
		->add_rule('match_pattern', '/^[0-9,]+$/');
	}

	private function regex_username()
	{
		$this->Val
		->add('username', 'GET username')
		->add_rule('required')
		->add_rule('match_pattern', '/^[\S\s]{4,20}$/');
		# /^[\S\s]{3,15}$/
	}

	private function regex_profile_img()
	{
		$this->Val
		->add('profile_img', 'GET profile_img')
		->add_rule('required')
		->add_rule('match_pattern', '/^[0-9_-]+$/');
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

	private function regex_url()
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

	private function regex_provider()
	{
		$this->Val
		->add('provider', 'GET provider')
		->add_rule('required')
		->add_rule('match_pattern', '/^(api.twitter.com)|(graph.facebook.com)$/');
	}

	private function regex_sns_token()
	{
		$this->Val
		->add('sns_token', 'GET sns_token')
		->add_rule('required')
		->add_rule('match_pattern', '/^\S{20,4000}$/');
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
		->add_rule('match_pattern', '/^[0-9.]{0,6}$/');
	}

	private function regex_model()
	{
		$this->Val
		->add('model', 'GET model')
		->add_rule('required')
		->add_rule('match_pattern', '/^[\S\s]{0,50}$/');
	}

	private function regex_device_token()
	{
		$this->Val
		->add('device_token', 'GET device_token')
		->add_rule('required')
		->add_rule('match_pattern', '/^([a-f0-9]{64})|([a-zA-Z0-9:_-]{140,250})$/');
	}

	private function regex_rest_id()
	{
		$this->Val
		->add('rest_id', 'GET rest_id')
		->add_rule('required')
		->add_rule('match_pattern', '/^[0-9]+$/');
	}

	private function regex_restname()
	{
		$this->Val
		->add('restname', 'GET restname')
		->add_rule('required')
		->add_rule('match_pattern', '/^[\S\s]{2,30}$/u');
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

	private function regex_post_id()
	{
		$this->Val
		->add('post_id', 'GET post_id')
		->add_rule('required')
		->add_rule('match_pattern', '/^[0-9]+$/');
	}

	private function regex_movie_name()
	{
		$this->Val
		->add('movie_name', 'GET movie_name')
		->add_rule('required')
		->add_rule('match_pattern', '/^[0-9_-]+$/');
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

	private function regex_value()
	{
		$this->Val
		->add('value_id', 'GET value_id')
		->add_rule('match_pattern', '/^[0-9]{0,7}$/');
	}

	private function regex_page()
	{
		$this->Val
		->add('page', 'GET page')
		->add_rule('match_pattern', '/^[0-9]$/');
	}

	private function regex_comment()
	{
		$this->Val
		->add('comment', 'GET comment')
		->add_rule('required')
		->add_rule('match_pattern', '/^[\S\s]{2,140}$/u');
	}

	private function regex_memo()
	{
		$this->Val
		->add('memo', 'GET memo')
		->add_rule('required')
		->add_rule('match_pattern', '/^[\S\s]{1,140}$/u');
	}

	private function regex_feedback()
	{
		$this->Val
		->add('feedback', 'GET feedback')
		->add_rule('required')
		->add_rule('match_pattern', '/^[\S\s]{4,300}$/u');
	}

	private function regex_cheer_flag()
	{
		$this->Val
		->add('cheer_flag', 'GET cheer_flag')
		->add_rule('match_pattern', '/^[01]$/');
	}

	//======================================================//

	//Valodation Check
	private function check($val_param)
	{
		$val = $this->Val;

		if($val->run($val_param)){
		    //OK
		    $this->safe_param = $val_param;

		}else{
			//エラー 形式不備
		    foreach($val->error() as $key=>$value){
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