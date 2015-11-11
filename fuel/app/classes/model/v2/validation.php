<?php

/**
*
*/

class Model_V2_Validation extends Model
{
	public static function check()
	{
		switch (Uri::string()) {

			case '/auth/signup':
				self::check_signup();
				break;

			case '/auth/login':
				self::check_login();
				break;

			case '/auth/sns_login':
				self::check_sns_login();
				break;

			case '/auth/pass_login':
				self::check_pass_login();
				break;

			default:
				self::error('ERROR_CONNECTION_FAILED');
				break;
		}
	}


	//======================================================//
	// URI list
	//======================================================//

	public static function check_signup($user_data)
	{
		$val = Validation::forge();

		$val = self::format_username($val);
		$val = self::format_os($val);
		$val = self::format_ver($val);
		$val = self::format_model($val);
		$val = self::format_register_id($val);

		self::run($val, $user_data);
		self::overlap_username($user_data['username']);
		self::overlap_register_id($user_data['register_id']);
	}

	public static function check_login($user_data)
	{
		$val = Validation::forge();

		$val = self::format_identity_id($val);

		self::run($val, $user_data);
		self::verify_identity_id($user_data['identity_id']);
	}

	public static function check_sns_login($user_data)
	{
		$val = Validation::forge();

		$val = self::format_identity_id($val);
		$val = self::format_os($val);
		$val = self::format_ver($val);
		$val = self::format_model($val);
		$val = self::format_register_id($val);

		self::run($val, $user_data);
		self::verify_identity_id($user_data['identity_id']);
		self::overlap_register_id($user_data['register_id']);
	}

	public static function check_pass_login($user_data)
	{
		$val = Validation::forge();

		$val = self::format_username($val);
		$val = self::format_password($val);
		$val = self::format_register_id($val);

		self::run($val, $user_data);
		self::verify_password($user_data['username'], $user_data['pass']);
	}


	//======================================================//
	// Validation methods
	//======================================================//

	private static function format_username($val)
	{
		$val->add('username', 'GET username')
		    ->add_rule('required')
		    ->add_rule('match_pattern', '/^\w{4,20}$/')

		return $val;
	}

	private static function format_os($val)
	{
		$val->add('os', 'GET os')
		    ->add_rule('required')
		    ->add_rule('match_pattern', '/^android$|^iOS$/');

		return $val;
	}

	private static function format_ver($val)
	{
		$val->add('ver', 'GET ver')
		    ->add_rule('required')
		    ->add_rule('match_pattern', '/^[0-9]+$/');

		return $val;
	}

	private static function format_model($val)
	{
		$val->add('model', 'GET model')
		    ->add_rule('required')
		    ->add_rule('match_pattern', '/^[a-zA-Z0-9_-]{0,10}$/');

		return $val;
	}

	private static function format_password($val)
	{
		$val->add('pass', 'GET password')
		    ->add_rule('required')
		    ->add_rule('match_pattern', '/^\w{6,25}$/');

		return $val;
	}

	private static function format_identity_id($val)
	{
		$val->add('identity_id', 'GET identity_id')
		    ->add_rule('required')
		    ->add_rule('match_pattern', '/^us-east-1:[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/');

		return $val;
	}

	private static function format_register_id($val)
	{
		$val->add('register_id', 'GET register_id')
		    ->add_rule('required');
		    ->add_rule('match_pattern', '/^([a-f0-9]{64})|([a-zA-Z0-9:_-]{140,250})$/');

		return $val;
	}

	//--------------------------------------------------------//

	//Valodation Check Run
	private static function run($val, $user_data)
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
    private static function overlap_username($username)
    {
        $result = Model_V2_Db_User::check_username($username);

        if (!empty($result)) {
        	//登録済み
            self::output_error('ERROR_USERNAME_ALREADY_REGISTERD');
            exit;
        }
    }

    //デバイス重複チェック
    private static function overlap_register_id($register_id)
    {
        $result = Model_V2_Db_Device::get_device_id($register_id);

        if (!empty($result)) {
        	//登録済み
            self::output_error('ERROR_REGISTER_ID_ALREADY_REGISTERD');
            exit;
        }
    }

    //identity_id未登録チェック
    private static function verify_identity_id($identity_id)
    {
    	$result = Model_V2_Db_User::check_identity_id($identity_id);

    	if (empty($result)) {
    		//登録なし
            self::output_error('ERROR_IDENTITY_ID_NOT_REGISTERD');
            Model_Cognito::delete_identity_id($identity_id);
            exit;
        }
    }


    private static function verify_password($username, $password)
    {
    	$hash_pass = Model_V2_Db_User::get_password($username);

    	if (empty($hash_pass)) {
    		//username登録なし
    		self::output_error('ERROR_USERNAME_NOT_REGISTERD');
    		exit;
    	}

        if (password_verify($password, $hash_pass)) {
            //認証OK
        } else {
        	//パスワード不一致
            self::output_error('ERROR_PASSWORD_WRONG');
            exit;
        }
    }



	//======================================================//
	// Error methods
	//======================================================//


    private static function output_error($error_code)
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