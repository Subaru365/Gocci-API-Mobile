<?php
/**
 * Aws-Cognito model Class.
 *
 * @package    Gocci-Mobile
 * @version    3.1.0 (2015/12/23)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @copyright  (C) 2015 Akira Murata
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
        $result = $this->getData(self::$dev_provider, $user_id);
        return $result['Token'];
    }

    public function getIid($user_id)
    {
        $result = $this->getData(self::$dev_provider, $user_id);
        return $result['IdentityId'];
    }

    public function getLoginData($user_id)
    {
        $result = $this->getData(self::$dev_provider, $user_id);
        return $result;
    }

    public function setSnsAccount($params)
    {
        //var_dump($params);
        try {
            $result = $this->addSnsAccount($params['identity_id'], $params['provider'], $params['sns_token']);
        } catch (Throwable $e) {
            $result = $this->getId($params['provider'], $params['sns_token']);
            if (!empty($result)) {
                $result = $this->deleteSnsAccount($result['IdentityId'], $params['provider'], $params['sns_token']);                
            }
        }
        return $result;
    }

    public function unsetSnsAccount($params)
    {
        try {
            $result = $this->deleteSnsAccount($params['identity_id'], $params['provider'], $params['sns_token']);
        } catch (Throwable $e) {
            $result = $this->getId($params['provider'], $params['sns_token']);
            if (!empty($result)) {
                $result = $this->deleteSnsAccount($result['IdentityId'], $params['provider'], $params['sns_token']);                
            }
        }
        return $result;
    }

    public function delete_identity_id($identity_id)
    {
        $this->delete_id($identity_id);
    }


    private function getId($provider, $token)
    {
        $result = $this->client->getId([
            'IdentityPoolId'    => self::$pool_id,
            'Logins'            => array("$provider" => "$token",),
        ]);
    }

    private function getData($provider, $token)
    {
        $result = $this->client->getOpenIdTokenForDeveloperIdentity([
            'IdentityPoolId'    => self::$pool_id,
            'Logins'            => array("$provider" => "$token",),
            'TokenDuration'     => 7200,
        ]);
        return $result;
    }

    private function addSnsAccount($identity_id, $provider, $token)
    {
        $result = $this->client->getOpenIdTokenForDeveloperIdentity([
            'IdentityPoolId'    => self::$pool_id,
            'IdentityId'        => "$identity_id",
            'Logins'            => array(
                self::$dev_provider => session::get('user_id'),
                "$provider" => "$token",
            ),
        ]);
    }

    public function deleteSnsAccount($identity_id, $provider, $token)
    {
        $result = $this->client->unlinkIdentity([
            'IdentityId'        => "$identity_id",
            'Logins'            => array("$provider"  => "$token",),
            'LoginsToRemove'    => array("$provider",),
        ]);
    }

    private function delete_id($identity_id)
    {
        $result = $this->client->deleteIdentities([
            'IdentityIdsToDelete' => ["$identity_id"],
        ]);
    }
}