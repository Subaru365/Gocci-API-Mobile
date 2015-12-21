<?php
/**
 * Aws-Sns Model Class.
 *
 * @package    Gocci-Mobile
 * @version    3.0.0 (2015/12/18)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @copyright  (C) 2015 Akira Murata
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
    public $type;


    private function __construct()
    {
        $this->client = new SnsClient([
			'region'  => 'ap-northeast-1',
    		'version' => '2010-03-31'
		]);
    }

    public function pushAndroid($username, $arn, $id)
    {
        try {
            $this->publishAndroid($username, $arn, $id);
        }
        catch (Exception $e) {
            error_log($arn . " Error!\n");
            $device = Model_V3_Db_Device::getInstance();
            $device->deleteDeviceForArn($arn);
            $this->deleteEndpoint($arn);
            exit;
        }
    }

    public function pushiOS($username, $arn, $id)
    {
        try {
            $this->publishiOS($username, $arn, $id);
        }
        catch (Exception $e) {
            error_log($arn . " Error!\n");
            $device = Model_V3_Db_Device::getInstance();
            $device->deleteDeviceForArn($arn);
            $this->deleteEndpoint($arn);
            exit;
        }
    }

    public function setSns($user_data)
    {
    	if ($user_data['os'] === 'android') {
            $result = self::set_android($user_data);
        } else {
        	$result = self::set_ios($user_data);
        }
        return $result['EndpointArn'];
    }

    public function deleteSns($endpoint_arn)
    {
        try {
    	   $this->deleteEndpoint($endpoint_arn);
        }
        catch (Exception $e){
            error_log($endpoint_arn.$e);
        }
    }

	private function set_android($params)
	{
		$android_arn = Config::get('_sns.android_ApplicationArn');

		$result = $this->client->createPlatformEndpoint([
    		'CustomUserData' 			=> "user_id / {$params['user_id']}",
    		'PlatformApplicationArn'    => "$android_arn",
    		'Token'                     => "$params[device_token]",
    	]);
    	return $result;
	}

	private function set_ios($params)
	{
		$iOS_arn = Config::get('_sns.iOS_ApplicationArn');

		$result = $this->client->createPlatformEndpoint([
    		'CustomUserData' 			=> "user_id / {$params['user_id']}",
    		'PlatformApplicationArn'    => "$iOS_arn",
    		'Token'                     => "$params[device_token]",
    	]);
    	return $result;
	}


	private function publishAndroid($username, $arn, $id)
	{
        $message = array(
            'type'      => "$this->type",
            'id'        => "$id",
            'username'  => "$username",
        );

        $message = json_encode(
            $message,
            JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|
            JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT
        );

	    $result = $this->client->publish([
            'Message'   => "$message",
            'TargetArn' => "$arn",
        ]);
	}


	private function publishiOS($username, $arn, $id)
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

	private function deleteEndpoint($endpoint_arn)
	{
		$this->client->deleteEndpoint([
    		'EndpointArn' => "$endpoint_arn",
		]);
	}
}