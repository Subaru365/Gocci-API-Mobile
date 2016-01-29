<?php
/**
 * Aws-Sns Model Class.
 *
 * @package    Gocci-Mobile
 * @version    3.2.1 (2016/1/22)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @copyright  (C) 2015 Akira Murata
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */
use Aws\Sns\SnsClient;

class Model_V4_Aws_Sns extends Model
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

    public function pushAndroid($params, $arn)
    {
        try {
            $this->publishAndroid($params['username'], $arn);
        }
        catch (Throwable $e) {
            error_log($arn . " Error!\n");
            $device = Model_V4_Db_Device::getInstance();
            $device->deleteDeviceForArn($arn);
            $this->deleteEndpoint($arn);
        }
    }

    public function pushiOS($params, $arn)
    {
        try {
            $data = $this->makePayload($params);
            $this->publishiOS($data, $arn);
        }
        catch (Throwable $e) {
            error_log($arn . " Error!\n");
            $device = Model_V4_Db_Device::getInstance();
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


	private function publishAndroid($username, $arn)
	{
        $message = array(
            'type'      => "$this->type",
            'id'        => 1,
            'username'  => $username,
        );

        $message = json_encode(
            $message,
            JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|
            JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT
        );

	    $result = $this->client->publish([
            'Message'   => $message,
            'TargetArn' => $arn,
        ]);
	}


    private function publishiOS($data, $arn)
    {
        $this->client->publish(array(

            'TargetArn' => $arn,
            'MessageStructure' => 'json',

            'Message' => json_encode(array
            (
                'APNS_SANDBOX' => json_encode(array
                (
                    //'background' => array();

                    'aps' => array(
                        'alert'    => $data['alert'],
                        'sound'    => 'default',
                        'badge'    => $data['badge'],
                    ),

                    'foreground' => array(
                        'badge'    => $data['badge'],
                        'type'     => "$this->type",
                        'payload'  => $data['payload'],
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


	private function makePayload($params)
	{
        switch ($this->type) {

            case 'like':
                $data = array(
                    'alert'     => "{$params['username']}さんにゴチされました！",
                    'badge'     => $params['badge'],
                    'payload'   => array(
                        'user_id'   => $params['a_user_id'],
                        'username'  => $params['username'],
                    ),
                );
                break;

            case 'comment':
                $data = array(
                    'alert'     => "{$params['username']}さんからコメントされました！",
                    'badge'     => $params['badge'],
                    'payload'   => array(
                        'user_id'   => $params['a_user_id'],
                        'username'  => $params['username'],
                        'post_id'   => $params['post_id'],
                        'comment'   => $params['comment'],
                    ),
                );
                break;

            case 'follow':
                $data = array(
                    'alert'     => "{$params['username']}さんからフォローされました！",
                    'badge'     => $params['badge'],
                    'payload'   => array(
                        'user_id'   => $params['a_user_id'],
                        'username'  => $params['username'],
                    ),
                );
                break;

            case 'post_complete':
                $data = array(
                    'alert'     => '投稿が完了しました！',
                    'badge'     => $params['badge'],
                    'payload'   => array(
                        'post_id'   => "$params[post_id]",
                        'rest_id'   => "$params[rest_id]",
                        'restname'  => "$params[restname]",
                    ),
                );
                break;

            case 'announcement':
                $data = array(
                    'alert'     => $params['alert'],
                    'payload'   => array(
                        'head'      => $params['alert'],
                        'body'      => $params['message'],
                    )
                );
                break;
        }

        return $data;
    }
}