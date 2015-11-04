<?php
/**
 * Authentication Class. Request SignUp, LogIn.
 *
 * @package    Gocci-Mobile
 * @version    3.0 (2015/10/29)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @license    MIT License
 * @copyright  2015 Inase,inc.
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */
use Aws\Sns\SnsClient;

class Model_V3_Aws_Sns extends Model
{
	/**
     * @var Instance $Client
     */
    private $Client;


    public function __construct()
    {
        $this->Client = new SnsClient([
			'region'  => 'ap-northeast-1',
    		'version' => '2010-03-31'
		]);
    }

    public function set_device($user_data)
    {
    	if ($user_data['os'] === 'android') {
            $result = self::set_android($user_data);

        } else {
        	$result = self::set_ios($user_data);
        }

        return $result['EndpointArn'];
    }

    public function delete_data($endpoint_arn)
    {
    	$this->delete_endpoint($endpoint_arn);
    }


	private function set_android($user_data)
	{
		$android_arn = Config::get('_sns.android_ApplicationArn');

		$result = $this->Client->createPlatformEndpoint([
    		'CustomUserData' 			=> 'user_id/'.session::get('useer_id'),
    		'PlatformApplicationArn'    => "$android_arn",
    		'Token'                     => "$user_data[reg_id]",
    	]);
    	return $result;
	}

	private function set_ios($user_data)
	{
		$iOS_arn = Config::get('_sns.iOS_ApplicationArn');

		$result = $this->Client->createPlatformEndpoint([
    		'CustomUserData' 			=> 'user_id/'.session::get('user_id'),
    		'PlatformApplicationArn'    => "$iOS_arn",
    		'Token'                     => "$user_data[reg_id]",
    	]);
    	return $result;
	}


	public function publish_android($keyword, $user_id, $target_user_id)
	{
        $username  = Model_User::get_name($user_id);
        $target_arn = Model_user_data::get_arn($target_user_id);

        $message = "$username" . 'さんから' . "$keyword" . 'されました！';

	    $result = $client->publish([
            'Message'   => "$message",
            'TargetArn' => "$target_arn",
        ]);
	}


	public function publish_ios($endpointArn, $alert)
	{
		$client->publish(array(

        	'TargetArn' => $endpointArn,
        	'MessageStructure' => 'json',

        	'Message' => json_encode(array
	        (
	        	'APNS_SANDBOX' => json_encode(array
	          	(
	                'aps' => array(
	                    'alert' => $alert,
	                    'sound' => 'default',
	                    'badge' => 1
	            	),
	            // カスタム
	         	//'custom_text' => "$message",
	        	)
	        	)
	    	)
	    	)
        ));
	}

	public function delete_arn($endpoint_arn)
	{
		$this->Client->deleteEndpoint([
    		'EndpointArn' => "$endpoint_arn",
		]);
	}
}