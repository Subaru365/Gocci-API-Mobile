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



	public function action_info()
	{

		$user_1 = array(
			'name' => 'buzz',
			'id'   => 1,
		);

		$user_2 = array(
			'name' => 'fuzz',
			'id'   => 2,
		);

		$user_data['new_user'] = $user_1;		
		$user_data['old_user'] = $user_2;

		$status['payload'] = $user_data;
		
        $json = json_encode(
            $status,
            JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|
            JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT
        );

        echo $json;
        exit;
    }

	// public static function push($endpointArn, $alert)
	// {

	// 	$client = new SnsClient([
	// 		'region'  => 'ap-northeast-1',
 	//    	'version' => '2010-03-31'
	// 	]);

	// 	$client->publish(array(

 	//    	'TargetArn' => $endpointArn,
 	//      'MessageStructure' => 'json',

 	//      'Message' => json_encode(array
	//         	(
	// 	        	'APNS_SANDBOX' => json_encode(array
	// 	          	(
	// 	                'aps' => array(
	// 	                    'alert' => $alert,
	// 	                    'sound' => 'default',
	// 	                    'badge' => 1
	// 	            ),
	// 	            // カスタム
	// 	         	//'custom_text' => "$message",
	// 	        	))
	// 	    	)
	// 	    )
 	//    ));
	// }
}
