<?php
/**
 * Aws-Sns Model Class.
 *
 * @package    Gocci-Mobile
 * @version    3.1.0 (2015/12/23)
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
        catch (Throwable $e) {
            error_log($arn . " Error!\n");
            $device = Model_V3_Db_Device::getInstance();
            $device->deleteDeviceForArn($arn);
            $this->deleteEndpoint($arn);
        }
    }

    public function pushiOS($username, $arn, $id, $badge)
    {
        try {
            $payload = makePayload($params);
            $this->publishiOS($params, $payload);
        }
        catch (Throwable $e) {
            error_log($arn . " Error!\n");
            $device = Model_V3_Db_Device::getInstance();
            $device->deleteDeviceForArn($arn);
            $this->deleteEndpoint($arn);
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
        catch (Throwable $e){
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


	private function makePayload($params)
	{
        switch ($this->type) {
            
            case 'like':
                $alert   = $params['username'].'さんにゴッチされました！';
                $payload = array(
                    'user_id'   => "$params[user_id]",
                    'username'  => "$params[username]",
                );
                break;
            
            case 'comment':
                $alert   = $params['username'].'さんからコメントされました！';
                $payload = array(
                    'user_id'   => "$params[user_id]",
                    'username'  => "$params[username]",
                    'post_id'   => "$params[post_id]",
                    'comment'   => "$params[comment]",
                );
                break;

            case 'follow':
                $alert   = $params['username'].'さんからフォローされました！';
                $payload = array(
                    'user_id'   => "$params[user_id]",
                    'username'  => "$params[username]",
                );
                break;

            case 'post_complete':
                $alert   = '投稿が完了しました！'.$params['restname'];
                $payload = array(
                    'post_id'   => "$params[post_id]",
                    'rest_id'   => "$params[rest_id]",
                    'restname'  => "$params[restname]",
                );                
                break;

            case 'announcement':
                $alert   = '';
                $payload = array(
                    'head'      => "",
                    'bosy'      => "$params[message]",
                );
                break;
        }

        return $payload;
    }


    private function publishiOS($arn, $badge, $alert, $payload)
    {
		$this->client->publish(array(

        	'TargetArn' => $params['arn'],
        	'MessageStructure' => 'json',

        	'Message' => json_encode(array
	        (
	        	'APNS_SANDBOX' => json_encode(array
	          	(
                    //'background' => array();

	                'aps' => array(
	                    'alert'    => "$alert",
	                    'sound'    => 'default',
	                    'badge'    => $badge,
	            	),

                    'foreground' => array(
                        'badge'    => $badge,
                        'type'     => "$this->type",
                        'payload'  => $payload,
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