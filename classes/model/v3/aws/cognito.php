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

use Aws\CognitoIdentity\CognitoIdentityClient;
use Aws\CognitoSync\CognitoSyncClient;

class Model_V3_Aws_Cognito extends Model
{
    /**
     * @var Instance $Client
     */
    private $Client;

    /**
     * @var String $identity_pool_id
     */
    private static $identity_pool_id;

    /** 
     * @var String $developer_provider
     */
    private static $developer_provider;


    public function __construct()
    {
        $this->Client = new CognitoIdentityClient([
            'region'    => 'us-east-1',
            'version'   => 'latest'
        ]);

        $config = Config::get('_cognito');
        self::$identity_pool_id   = $config['IdentityPoolId'];
        self::$developer_provider = $config['developer_provider'];
    }


    public function get_token()
    {
        $result = $this->my_data();
        return $result['Token'];
    }

    public function get_data()
    {
        $result = $this->my_data();
        return $result;
    }

    public function delete_identity_id($identity_id)
    {
        $this->delete_id($identity_id);
    }


    private function my_data()
    {
        $identity_pool_id   = self::$identity_pool_id;
        $developer_provider = self::$developer_provider;

        $result = $this->Client->getOpenIdTokenForDeveloperIdentity([
            'IdentityPoolId'    => "$identity_pool_id",
            'Logins'            => [
                "$developer_provider" => session::get('user_id'),
            ]
        ]);
        return $result;
    }

    private function delete_id($identity_id)
    {
        $result = $this->Client->deleteIdentities([
            'IdentityIdsToDelete' => ["$identity_id"],
        ]);
    }


 //    private function get_cognito()    
 //    {
 //        $config = Config::get('_cognito');

 //        $result = self::$Client->getOpenIdTokenForDeveloperIdentity([
 //            'IdentityId'        => "$identity_id",
 //            'IdentityPoolId'    => "$config[IdentityPoolId]",
 //            'Logins'            => [
 //                "$config[developer_provider]" => session::get('user_id'),
 //            ]
 //        ]);
 //        return $result['Token'];
 //    }


	// public static function set_data()
	// {
 //       $Client = self::set_Client();
	// 	$config  = Config::get('_cognito');
 //        $user_id = session::get('user_id');

 //        $result = $Client->getOpenIdTokenForDeveloperIdentity
 //        ([
 //            'IdentityPoolId'    => "$config[IdentityPoolId]",
 //            'Logins'            => [
 //                "$config[developer_provider]" => "$user_id",
 //            ]
 //        ]);
	// 	return $result;
	// }


 //    //=========================================================================//

 //    //SNS連携
 //    public static function post_sns($data)
 //    {
 //        $cognito_data = Config::get('_cognito');

 //        $Client = new CognitoIdentityClient([
 //            'region'    => 'us-east-1',
 //            'version'   => 'latest'
 //        ]);

 //        $result = $Client->getOpenIdTokenForDeveloperIdentity([
 //            'IdentityId'        => "$data['identity_id']",
 //            'IdentityPoolId'    => "$cognito_data[IdentityPoolId]",
 //            'Logins'            => [
 //                "$cognito_data[developer_provider]" => session::get('user_id'),
 //                "$provider" => "$data['token']",
 //            ],
 //        ]);

 //        return $result;
 //    }


 //    public static function delete_sns($identity_id, $provider, $token)
 //    {
 //        $developer_provider = Config::get('_cognito.developer_provider');

 //        $result = $Client->unlinkIdentity([
 //            'IdentityId'        => "$identity_id",
 //            'Logins'            => ["$provider" => "$token"],
 //            'LoginsToRemove'    => ["$provider"],
 //        ]);
 //    }
}