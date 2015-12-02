<?php
/**
 * Aws-Sns Model Class.
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
    use SingletonTrait;

	/**
     * @var Instance $client
     */
    private $client;

    /**
     * @var Instance $type
     */
    public $message;


    private function __construct()
    {
        $this->client = new SnsClient([
			'region'  => 'ap-northeast-1',
    		'version' => '2010-03-31'
		]);
    }

    public function pushAndroid($username, $arn)
    {
        try {
            $this->publishAndroid($username, $arn);
        }
        catch (Exception $e) {
            error_log($arn . " Error!\n");
            exit;
        }
    }

    public function pushiOS($username, $arn)
    {
        try {
            $this->publishiOS($username, $arn);
        }
        catch (Exception $e) {
            error_log($arn . " Error!\n");
            exit;
        }

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
        try {
    	   $this->delete_endpoint($endpoint_arn);
        }
        catch (Exception $e){
            error_log($e);
        }
    }

	private function set_android($params)
	{
		$android_arn = Config::get('_sns.android_ApplicationArn');

		$result = $this->client->createPlatformEndpoint([
    		'CustomUserData' 			=> 'user_id/'.$params['user_id'],
    		'PlatformApplicationArn'    => "$android_arn",
    		'Token'                     => "$params[register_id]",
    	]);
    	return $result;
	}

	private function set_ios($params)
	{
		$iOS_arn = Config::get('_sns.iOS_ApplicationArn');

		$result = $this->client->createPlatformEndpoint([
    		'CustomUserData' 			=> 'user_id/'.$params['user_id'],
    		'PlatformApplicationArn'    => "$iOS_arn",
    		'Token'                     => "$params[register_id]",
    	]);
    	return $result;
	}


	private function publishAndroid($username, $arn)
	{
            $message = "$username" . 'さんから' . $this->message . 'されました！';

    	    $result = $this->client->publish([
                'Message'   => "$message",
                'TargetArn' => "$arn",
            ]);
	}


	private function publishiOS($endpointArn, $alert)
	{
		$this->client->publish(array(

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

	private function delete_endpoint($endpoint_arn)
	{
		$this->client->deleteEndpoint([
    		'EndpointArn' => "$endpoint_arn",
		]);
	}
}