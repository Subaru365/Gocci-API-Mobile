<?php
/**
 * Authentication Class. Request SignUp, LogIn.
 *
 * @package    Gocci-Mobile
 * @version    3.0 (2015/11/03)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @license    MIT License
 * @copyright  2015 Inase,inc.
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

        $this->req_params = $params;
        $this->output_success();
    }


    private function getLoginData($identity_id)
    {
        $user       = Model_V3_Db_User::getInstance();
        $cognito    = Model_V3_Aws_Cognito::getInstance();

        $user_data  = $user->getUser($identity_id);
        if (empty($user_data)) {
            $this->status = Model_V3_Status::getStatus('ERROR_IDENTITY_ID_NOT_REGISTERD');
            $this->output();
        }

        $params = $user_data[0];
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

        $this->req_params['identity_id'] = $params['identity_id'];
        $this->output_success();
    }

    private function chkOverlapUsername($username)
    {
        $user = Model_V3_Db_User::getInstance();

        if ($user->getIdForName($username)) {
            $this->status = Model_V3_Status::getStatus('ERROR_USERNAME_ALREADY_REGISTERD');
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

        $this->req_params['identity_id'] = $identity_id;
        $this->output_success();
    }

    private function chkPassword($username, $password)
    {
        $user = Model_V3_Db_User::getInstance();

        $hash_pass = $user->getPassword($username);
        if (empty($hash_pass)) {
            $this->status = Model_V3_Status::getStatus('ERROR_PASSWORD_NOT_REGISTERD');
            $this->output();

        } else if (password_verify($password, $hash_pass[0]['password'])) {
            //認証OK
            $identity_id = $user->getIdentityId($username);
            return $identity_id;

        } else {
            $this->status = Model_V3_Status::getStatus('ERROR_PASSWORD_WRONG');
            $this->output();
        }
    }
}

    //======================================================//
    // Functions
    //======================================================//

//     private function chk_register_id()
//     {
//         $result = $this->Device->get_user($this->req_params['register_id']);

//         if (!empty($result)) {
//             //端末登録あり
//             $identity_id  = $this->User->get_identity($result[0]['device_user_id']);
//             $this->status = Model_V3_Status::getStatus('ERROR_REGISTER_ID_ALREADY_REGISTERD', $identity_id);
//             $this->output();
//         }
//     }


//     private function chk_identity_id($identity_id)
//     {
//         $result = $this->User->get_id($identity_id);

//         if (empty($result)) {
//             $this->status = Model_V3_Status::getStatus('ERROR_IDENTITY_ID_NOT_REGISTERD');
//             $this->Cognitto->delete_id($identity_id);
//             $this->output();
//         }
//         return $result[0]['user_id'];
//     }


//     private function chk_username($username)
//     {
//         $result = $this->User->check_name($username);

//         if (empty($result)) {
//             $this->status = Model_V3_Status::getStatus('ERROR_USERNAME_NOT_REGISTERD');
//             $this->output();
//         }
//         return $result[0]['user_id'];
//     }


//     private function chk_overlap_register_id($register_id)
//     {
//         $device_arn = $this->Device->check_arn($register_id);

//         if (!empty($device_arn)) {
//             $this->status = Model_V3_Status::getStatus('ERROR_REGISTER_ID_ALREADY_REGISTERD');
//             $this->output();
//         }
//     }

//     //======================================================//


//     private function update_device()
//     {
//         //外部処理 Link:Controller/bgp/update_device
//         $req_params = $this->req_params;
//         $ch = curl_init();

//         curl_setopt($ch, CURLOPT_URL,
//             'http://localhost/v3/bgp/update_device/'
//             .'?user_id='     . "$req_params[user_id]"
//             .'&os='          . "$req_params[os]"
//             .'&ver='         . "$req_params[ver]"
//             .'&model='       . "$req_params[model]"
//             .'&register_id=' . "$req_params[register_id]"
//         );
//         curl_exec($ch);
//         curl_close($ch);
//     }
// }