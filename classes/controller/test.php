<?php
use Aws\Sns\SnsClient;
/**
*
*/
class Controller_Test extends Controller
{
	private $test;

	public function before()
	{
		$this->test = "hoge";
	}

	public function action_index()
	{
		$val_params = array(
			'message' => Input::get('message'),
		);

		$validation = validation::forge('request');

		$validation->add('comment', 'comment')
    	->add_rule('match_pattern', '/^.{2,140}$/u')
    	->add_rule('required');


    	if($validation->run($val_params)){
		    //OK
		    $safe_param = $val_param;

		}else{
			//エラー 形式不備
		    foreach($validation->error() as $key=>$value){
		    	$keys[]		= $key;
		    	$messages[] = $value;
		    }

		    $key 		= implode(", ", $keys);
		    $message    = implode(". ", $messages);

		    error_log("$message");
		    $safe_param['message'] = array('error');
		}

		$json = json_encode(
            $safe_param,
            JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|
            JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT
        );

        echo $json;
	}

	// public function action_info()
	// {

	// 	$user_1 = array(
	// 		'name' => 'buzz',
	// 		'id'   => 1,
	// 	);

	// 	$user_2 = array(
	// 		'name' => 'fuzz',
	// 		'id'   => 2,
	// 	);

	// 	$user_data['new_user'] = $user_1;
	// 	$user_data['old_user'] = $user_2;

	// 	$status['payload'] = $user_data;

 //        $json = json_encode(
 //            $status,
 //            JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|
 //            JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT
 //        );

 //        echo $json;
 //        exit;
 //    }

}
