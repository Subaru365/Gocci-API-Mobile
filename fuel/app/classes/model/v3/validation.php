<?php
/**
 * Status Code and Message list.
 *
 * @package    Gocci-Mobile
 * @version    3.0 (2015/10/22)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @license    MIT License
 * @copyright  2015 Inase,inc.
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

class Model_V2_Validation extends Model
{
	/** @var Array $data */
	private $data;

	/** @var Object $val */
	private $val;


	private function before($data)
	{
		$this->data = $data;
	}


	public function check()
	{
		switch (Uri::string()) {

			case 'v3/auth/signup':
				$this->check_signup();
				break;

			case 'v3/auth/login':
				$this->check_login();
				break;

			case 'v3/auth/sns_login':
				$this->check_sns_login();
				break;

			case 'v3/auth/pass_login':
				$this->check_pass_login();
				break;

			default:
				$this->error('ERROR_CONNECTION_FAILED');
				break;
		}
	}


	//======================================================//
	// URI list
	//======================================================//

	public function check_signup($user_data)
	{
		$val = Validation::forge();

		$val = $this->regex_username($val);
		$val = $this->regex_os($val);
		$val = $this->regex_ver($val);
		$val = $this->regex_model($val);
		$val = $this->regex_register_id($val);

		$this->run($val, $user_data);
		$this->overlap_username($user_data['username']);
		$this->overlap_register_id($user_data['register_id']);
	}

	public function check_login($user_data)
	{
		$val = Validation::forge();

		$val = $this->regex_identity_id($val);

		$this->run($val, $user_data);
		$this->verify_identity_id($user_data['identity_id']);
	}

	public function check_sns_login($user_data)
	{
		$val = Validation::forge();

		$val = $this->regex_identity_id($val);
		$val = $this->regex_os($val);
		$val = $this->regex_ver($val);
		$val = $this->regex_model($val);
		$val = $this->regex_register_id($val);

		$this->run($val, $user_data);
		$this->verify_identity_id($user_data['identity_id']);
		$this->overlap_register_id($user_data['register_id']);
	}

	public function check_pass_login($user_data)
	{
		$val = Validation::forge();

		$val = $this->regex_username($val);
		$val = $this->regex_password($val);
		$val = $this->regex_register_id($val);

		$this->run($val, $user_data);
		$this->verify_password($user_data['username'], $user_data['pass']);
	}


	//======================================================//
	// Validation methods
	//======================================================//

	private function regex_username($val)
	{
		$val->add('username', 'GET username')
		    ->add_rule('required')
		    ->add_rule('match_pattern', '/^\w{4,20}$/')

		return $val;
	}

	private function regex_os($val)
	{
		$val->add('os', 'GET os')
		    ->add_rule('required')
		    ->add_rule('match_pattern', '/^android$|^iOS$/');

		return $val;
	}

	private function regex_ver($val)
	{
		$val->add('ver', 'GET ver')
		    ->add_rule('required')
		    ->add_rule('match_pattern', '/^[0-9]+$/');

		return $val;
	}

	private function regex_model($val)
	{
		$val->add('model', 'GET model')
		    ->add_rule('required')
		    ->add_rule('match_pattern', '/^[a-zA-Z0-9_-]{0,10}$/');

		return $val;
	}

	private function regex_password($val)
	{
		$val->add('pass', 'GET password')
		    ->add_rule('required')
		    ->add_rule('match_pattern', '/^\w{6,25}$/');

		return $val;
	}

	private function regex_identity_id($val)
	{
		$val->add('identity_id', 'GET identity_id')
		    ->add_rule('required')
		    ->add_rule('match_pattern', '/^us-east-1:[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/');

		return $val;
	}

	private function regex_register_id($val)
	{
		$val->add('register_id', 'GET register_id')
		    ->add_rule('required');
		    ->add_rule('match_pattern', '/^([a-f0-9]{64})|([a-zA-Z0-9:_-]{140,250})$/');

		return $val;
	}

	//--------------------------------------------------------//

	//Valodation Check Run
	private function run($val, $user_data)
	{
		if($val->run($user_data)){
		    //OK

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

		    exit;
		}
	}


	//======================================================//
	// DB check methods
	//======================================================//

	//ユーザー名重複チェック
    private function overlap_username($username)
    {
        $result = Model_V2_Db_User::check_username($username);

        if (!empty($result)) {
        	//登録済み
            $this->output_error('ERROR_USERNAME_ALREADY_REGISTERD');
            exit;
        }
    }

    //デバイス重複チェック
    private function overlap_register_id($register_id)
    {
        $result = Model_V2_Db_Device::get_device_id($register_id);

        if (!empty($result)) {
        	//登録済み
            $this->output_error('ERROR_REGISTER_ID_ALREADY_REGISTERD');
            exit;
        }
    }

    //identity_id未登録チェック
    private function verify_identity_id($identity_id)
    {
    	$result = Model_V2_Db_User::check_identity_id($identity_id);

    	if (empty($result)) {
    		//登録なし
            $this->output_error('ERROR_IDENTITY_ID_NOT_REGISTERD');
            Model_Cognito::delete_identity_id($identity_id);
            exit;
        }
    }


    private function verify_password($username, $password)
    {
    	$hash_pass = Model_V2_Db_User::get_password($username);

    	if (empty($hash_pass)) {
    		//username登録なし
    		$this->output_error('ERROR_USERNAME_NOT_REGISTERD');
    		exit;
    	}

        if (password_verify($password, $hash_pass)) {
            //認証OK
        } else {
        	//パスワード不一致
            $this->output_error('ERROR_PASSWORD_WRONG');
            exit;
        }
    }



	//======================================================//
	// Error methods
	//======================================================//


    private function output_error($error_code)
    {
    	switch ($error_code) {

    		case 'ERROR_CONNECTION_FAILED':
    			$error_data = array(
    				'code'		=> 'ERROR_CONNECTION_FAILED',
    				'message'	=> 'Server connection failed'
    			);
    			break;

    		case 'ERROR_USERNAME_ALREADY_REGISTERD':
    			$error_data = array(
    				'code'		=> 'ERROR_USERNAME_ALREADY_REGISTERD',
    				'message'	=> 'The provided username was already registerd by another user'
    			);
    			break;

    		case 'ERROR_REGISTER_ID_ALREADY_REGISTERD':
				$error_data = array(
    				'code'		=> 'ERROR_REGISTER_ID_ALREADY_REGISTERD',
    				'message'	=> 'This deviced already has an registerd account'
    			);
    			break;

    		case 'ERROR_IDENTITY_ID_NOT_REGISTERD':
    			$error_data = array(
    				'code'		=> 'ERROR_IDENTITY_ID_NOT_REGISTERD',
    				'message'	=> 'The provided identity_id is not bound to any account'
    			);
    			break;

    		case 'ERROR_USERNAME_NOT_REGISTERD':
    			$error_data = array(
    				'code'		=> 'ERROR_USERNAME_NOT_REGISTERD',
    				'message'	=> 'The entered username does not exist'
    			);
    			break;

    		case 'ERROR_PASSWORD_WRONG':
    			$error_data = array(
    				'code'		=> 'ERROR_PASSWORD_WRONG',
    				'message'	=> 'Password wrong'
    			);
    			break;

    		default:
    			$error_data = array(
    				'code'		=> 'ERROR_UNKNOWN_ERROR',
    				'message'	=> 'Unknown global error'
    			);
    			break;
    	}

    	Controller_V2_Mobile_Base::output_error($error_data);
    }
}