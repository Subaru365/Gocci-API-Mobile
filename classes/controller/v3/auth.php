<?php
/**
 * Authentication Class. Request SignUp, LogIn.
 *
 * @package    Gocci-Mobile
 * @version    3.0.0 (2015/11/17)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @copyright  2015 Akira Murata
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

class Controller_V3_Auth extends Controller_V3_Public
{
    public function before()
    {
        parent::before();
    }

    //==============================================================//

    public function action_login()
    {
        //req_params is [identity_id]
        $params = $this->getLoginData($this->req_params['identity_id']);
        session::set('user_id', $params['user_id']);
        $login = Model_V3_Db_Login::getInstance();
        $login->setLogin();

        $this->res_params = $params;
        $this->outputSuccess();
    }


    private function getLoginData($identity_id)
    {
        $user       = Model_V3_Db_User::getInstance();
        $cognito    = Model_V3_Aws_Cognito::getInstance();

        $user_data  = $user->getUser($identity_id);
        if (empty($user_data)) {
            $param = Model_V3_Param::getInstance();
            $param->set_auth_login_ERROR_IDENTITY_ID_NOT_REGISTERD();
            $this->output();
        }

        $params = $user_data[0];
        $params['badge_num']        = (int)$params['badge_num'];
        $params['profile_img']      = Model_V3_Transcode::decode_profile_img($params['profile_img']);
        $cognito_data               = $cognito->getLoginData($params['user_id']);
        $params['identity_id']      = $cognito_data['IdentityId'];
        $params['cognito_token']    = $cognito_data['Token'];
        return $params;
    }

    //==============================================================//

    public function action_signup()
    {
        //$req_params is [username]
        $user   = Model_V3_Db_User::getInstance();
        $device = Model_V3_Db_Device::getInstance();

        $this->chkOverlapUsername($this->req_params['username']);
        $params = $this->createProfile($this->req_params['username']);

        $user->setData($params);

        $this->res_params['identity_id'] = $params['identity_id'];
        $this->outputSuccess();
    }

    private function chkOverlapUsername($username)
    {
        $user = Model_V3_Db_User::getInstance();

        if ($user->getIdForName($username)) {
            $param = Model_V3_Param::getInstance();
            $param->set_auth_signup_ERROR_USERNAME_ALREADY_REGISTERD();
            $this->output();
        }
    }

    private function createProfile($username)
    {
        $user       = Model_V3_Db_User::getInstance();
        $cognito    = Model_V3_Aws_Cognito::getInstance();

        $params['user_id']      = $user->getNextId();
        $params['username']     = $username;
        $params['profile_img']  = '0_tosty_' . mt_rand(1, 7);
        $params['identity_id']  = $cognito->getIid($params['user_id']);

        return $params;
    }

    //==============================================================//

    public function action_password()
    {
        //$req_params is [username, password]
        $identity_id = $this->chkPassword($this->req_params['username'], $this->req_params['password']);

        $this->res_params['identity_id'] = $identity_id;
        $this->outputSuccess();
    }

    private function chkPassword($username, $password)
    {
        $user = Model_V3_Db_User::getInstance();

        if (empty($user->getIdForName($username))) {
            $param = Model_V3_Param::getInstance();
            $param->set_auth_password_ERROR_USERNAME_NOT_REGISTERD();
        }

        $hash_pass = $user->getPassword($username);
        if (empty($hash_pass)) {
            $param = Model_V3_Param::getInstance();
            $param->set_auth_password_ERROR_PASSWORD_NOT_REGISTERD();
            $this->output();

        } else if (password_verify($password, $hash_pass[0]['password'])) {
            //認証OK
            $identity_id = $user->getIdentityId($username);
            return $identity_id;

        } else {
            $param = Model_V3_Param::getInstance();
            $param->set_auth_password_ERROR_PASSWORD_WRONG();
            $this->output();
        }
    }
}
