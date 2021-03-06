<?php

/**
*
*/

class Model_V1_Validation extends Model
{
	public static function check_signup($username, $password)
	{
		$val = Validation::forge();

		$val = self::format_username($val);
		$val = self::format_password($val);

		//$val = self::format_register_id($val);

		self::run($val, $username, $password);
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


	//Valodation Check
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
	// Validation methods
	//======================================================//

	private static function format_username($val)
	{
		$val->add('username', 'POST username')
		    ->add_rule('required')
		    ->add_rule('max_length', 15);

		return $val;
	}

	private static function format_password($val)
	{
		$val->add('pass', 'POST password')
		    ->add_rule('required')
		    ->add_rule('min_length', 5)
		    ->add_rule('max_length', 20);

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
		    ->add_rule('required')
		    ->add_rule('match_pattern', '/^[a-zA-Z0-9.-_]{400,2200}$/');

		return $val;
	}

	//----------------------------------------------------------------//


	//ユーザー名重複チェック
    private static function overlap_username($username)
    {
        $result = Model_V1_V2_Db_User::get_user_id($username);

        if (!empty($result)) {
        	//登録済み
            Controller_V2_Mobile_Base::output_error(301);
            exit;
        }
    }

    //デバイス重複チェック
    private static function overlap_register_id($register_id)
    {
        $result = Model_V1_V2_Db_Device::get_device_id($register_id);

        if (!empty($result)) {
        	//登録済み
            Controller_V2_Mobile_Base::output_error(302);
            exit;
        }
    }

    //identity_id未登録チェック
    private static function verify_identity_id($identity_id)
    {
    	$result = Model_V1_V2_Db_User::get_user_id($identity_id);

    	if (empty($result)) {
    		//登録なし
            Controller_V2_Mobile_Base::output_error(303);
            Model_V1_Cognito::delete_identity_id($identity_id);
            exit;
        }
    }

    private static function verify_password($username, $password)
    {
    	$hash_pass = Model_V1_V2_Db_User::get_password($username);

    	if (empty($hash_pass)) {
    		//username登録なし
    		Controller_V2_Mobile_Base::output_error(304);
    		exit;
    	}

        if (password_verify($password, $hash_pass)) {
            //認証OK
        } else {
        	//パスワード不一致
            Controller_V2_Mobile_Base::output_none(305);
            exit;
        }
    }
}
