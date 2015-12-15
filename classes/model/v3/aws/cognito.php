<?php
/**
 * Aws-Cognito model Class.
 *
 * @package    Gocci-Mobile
 * @version    3.0 (2015/11/24)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @license    MIT License
 * @copyright  2015 Inase,inc.
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

use Aws\CognitoIdentity\CognitoIdentityClient;
use Aws\CognitoSync\CognitoSyncClient;

class Model_V3_Aws_Cognito extends Model
{
    use SingletonTrait;

    /**
     * @var Instance $client
     */
    private $client;

    /**
     * @var String $pool_id
     */
    private static $pool_id;

    /**
     * @var String $dev_provider
     */
    private static $dev_provider;


    private function __construct()
    {
        $this->client = new CognitoIdentityClient([
            'region'    => 'us-east-1',
            'version'   => 'latest'
        ]);

        $config = Config::get('_cognito');
        self::$pool_id      = $config['IdentityPoolId'];
        self::$dev_provider = $config['developer_provider'];
    }

    public function getToken($user_id)
    {
        $result = $this->getData($user_id);
        return $result['Token'];
    }

    public function getIid($user_id)
    {
        $result = $this->getData($user_id);
        return $result['IdentityId'];
    }

    public function getLoginData($user_id)
    {
        $result = $this->getData($user_id);
        return $result;
    }

    public function setSnsAccount($params)
    {
        $result = $this->addSnsAccount($params['provider'], $params['sns_token']);
        return $result;
    }

    public function unsetSnsAccount($params)
    {
        $result = $this->deleteSnsAccount($params);
        return $result;
    }

    public function delete_identity_id($identity_id)
    {
        $this->delete_id($identity_id);
    }


    private function getData($user_id)
    {
        $result = $this->client->getOpenIdTokenForDeveloperIdentity([
            'IdentityPoolId'    => self::$pool_id,
            'Logins'            => [
                self::$dev_provider => "$user_id",
            ],
            'TokenDuration'     => 7200,
        ]);
        return $result;
    }

    private function addSnsAccount($provider, $token)
    {
        $result = $this->client->getOpenIdTokenForDeveloperIdentity([
            'IdentityPoolId'    => self::$pool_id,
            'Logins'            => [
                self::$dev_provider => session::get('user_id'),
                $provider => $token,
            ],
        ]);
    }

    public function deleteSnsAccount($params)
    {
        $result = $this->client->unlinkIdentity([
            'IdentityId'        => $params['identity_id'],
            'Logins'            => [
                $params['provider']  => $params['sns_token']],
            'LoginsToRemove'    => [$params['provider']],
        ]);
    }

    private function delete_id($identity_id)
    {
        $result = $this->client->deleteIdentities([
            'IdentityIdsToDelete' => ["$identity_id"],
        ]);
    }


 //    private function get_cognito()
 //    {
 //        $config = Config::get('_cognito');

 //        $result = self::$client->getOpenIdTokenForDeveloperIdentity([
 //            'IdentityId'        => "$identity_id",
 //            'IdentityPoolId'    => "$config[IdentityPoolId]",
 //            'Logins'            => [
 //                "$config[dev_provider]" => session::get('user_id'),
 //            ]
 //        ]);
 //        return $result['Token'];
 //    }


	// public static function set_data()
	// {
 //       $client = self::set_client();
	// 	$config  = Config::get('_cognito');
 //        $user_id = session::get('user_id');

 //        $result = $client->getOpenIdTokenForDeveloperIdentity
 //        ([
 //            'IdentityPoolId'    => "$config[IdentityPoolId]",
 //            'Logins'            => [
 //                "$config[dev_provider]" => "$user_id",
 //            ]
 //        ]);
	// 	return $result;
	// }



 //    public static function delete_sns($identity_id, $provider, $token)
 //    {
 //        $dev_provider = Config::get('_cognito.dev_provider');

 //        $result = $client->unlinkIdentity([
 //            'IdentityId'        => "$identity_id",
 //            'Logins'            => ["$provider" => "$token"],
 //            'LoginsToRemove'    => ["$provider"],
 //        ]);
 //    }
}