<?php
header('Content-Type: application/json; charset=UTF-8');
/**
*
*/

class Controller_V2_Mobile_Base extends Controller
{
	//Check session
	public function before()
	{
		$user_id = session::get('user_id');

		if(empty($user_id)) {
            $error_data = array(
                'code'      => 'ERROR_SESSION_EXPIRED',
                'message'   => 'Session cookie is not valid anymore'
            );
			self::output_error($error_data);
			error_log('UnAuthorized Accsess.');
			exit;

        } else {
            $input_data = array_merge (Input::get(), Input::post());
            $data       = Model_V2_Validation::check($input_data);
        }
	}


    public static function output_success($payload_data)
    {
        $api_data = array(
            'version'   => 2.0,
            'uri'       => Uri::string(),
            'code'      => 'SUCCESS',
            'message'   => 'Successful API request',
            'payload'   => $payload_data
        );

        self::output_json($api_data);
    }


    public static function output_validation_error($key, $message)
    {
        $api_data = array(
            'version'   => 2.0,
            'uri'       => Uri::string(),
            'code'      => "$key VALIDATION ERROR",
            'message'   => "$message",
            'payload'   => 0
        );

        self::output_json($api_data);
    }


    public static function output_error($error_data)
    {
        $api_data = array(
            'version'   => 2.0,
            'uri'       => Uri::string(),
            'code'      => $error_data['code'],
            'message'   => $error_data['message'],
            'payload'   => 0
        );

        self::output_json($api_data);
    }


	public static function output_json($api_data)
	{
		$json = json_encode(
			$api_data,
			JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT
		);

		echo $json;
	}
}


