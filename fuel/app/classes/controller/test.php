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
		if ($_SERVER['REMOTE_ADDR'] !== '118.238.250.166') die();
		phpinfo();
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
