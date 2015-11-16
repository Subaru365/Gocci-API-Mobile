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
    /**
     * @var Instance $Cognito
     */
    private $Cognito;

    /**
     * @var Instance $Sns
     */
    private $Sns;


    public function before()
    {
        parent::before();

        $this->User    = new Model_V3_Db_User();
        $this->Device  = new Model_V3_Db_Device();
        $this->Cognito = new Model_V3_Aws_Cognito();
        $this->Sns     = new Model_V3_Aws_Sns();
    }

    public function action_login()
    {
        //req_params is [identity_id]
        $this->login();
        $this->output_success();
    }

    public function action_check()
    {
        //req_params is [register_id]
        $this->chk_register_id();
        $this->output_success();
    }

    public function action_signup()
    {
        //$req_params is [username, os, ver, model, register_id]
        $this->chk_overlap_register_id($this->req_params['register_id']);
        $this->chk_overlap_username($this->req_params['username']);

        $this->create_user();
        $this->output_success();
    }


    public function action_sns_login()
    {
        //Input $this->req_params is [identity, os, ver, model, register_id]
        $this->chk_overlap_register_id($this->req_params['register_id']);
        $this->req_params['user_id'] = $this->chk_identity_id($this->req_params['identity_id']);

        $this->update_device();
        $this->login();
        $this->output_success();
    }


    public function action_pass_login()
    {
        //Input $this->req_params is [username, pass, os, model, register_id]
        $this->chk_overlap_register_id($this->req_params['register_id']);
        $this->req_params['user_id'] = $this->chk_username($this->req_params['username']);
        $this->chk_password($this->req_params['username'], $this->req_params['password']);

        $this->update_device();
        $this->login();
        $this->output_success();
    }


    //======================================================//
    // Functions
    //======================================================//

    private static function set_session($user_id)
    {
        Session::set('user_id', "$user_id");
    }

    private static function get_rand_profile_img()
    {
        $profile_img = '2014-09-06_0_tosty_' . mt_rand(1, 7);
        return $profile_img;
    }


    private function chk_register_id()
    {
        $result = $this->Device->get_user($this->req_params['register_id']);

        if (!empty($result)) {
            //端末登録あり
            $identity_id  = $this->User->get_identity($result[0]['device_user_id']);
            $this->status = Model_V3_Status::getStatus('ERROR_REGISTER_ID_ALREADY_REGISTERD', $identity_id[0]);
            $this->output();
        }
    }


    private function chk_identity_id($identity_id)
    {
        $result = $this->User->get_id($identity_id);

        if (empty($result)) {
            $this->status = Model_V3_Status::getStatus('ERROR_IDENTITY_ID_NOT_REGISTERD');
            $this->Cognitto->delete_id($identity_id);
            $this->output();
        }
        return $result[0]['user_id'];
    }


    private function chk_username($username)
    {
        $result = $this->User->check_name($username);

        if (empty($result)) {
            $this->status = Model_V3_Status::getStatus('ERROR_USERNAME_NOT_REGISTERD');
            $this->output();
        }
        return $result[0]['user_id'];
    }


    private function chk_password($username, $password)
    {
        $hash_pass = $this->User->get_password($username);

        if (empty($hash_pass)) {
            $this->status = Model_V3_Status::getStatus('ERROR_PASSWORD_NOT_REGISTERD');
            $this->output();

        } else if (password_verify($password, $hash_pass[0]['password'])) {
            //認証OK
            $result = $this->User->get_identity($this->req_params['user_id']);
            $this->req_params['identity_id'] = $result[0]['identity_id'];

        } else {
            $this->status = Model_V3_Status::getStatus('ERROR_PASSWORD_WRONG');
            $this->output();
        }
    }


    private function chk_overlap_username($username)
    {
        $result = $this->User->check_name($username);

        if (!empty($result)) {
            $this->status = Model_V3_Status::getStatus('ERROR_USERNAME_ALREADY_REGISTERD');
            $this->output();
        }
    }


    private function chk_overlap_register_id($register_id)
    {
        $device_arn = $this->Device->check_arn($register_id);

        if (!empty($device_arn)) {
            $this->status = Model_V3_Status::getStatus('ERROR_REGISTER_ID_ALREADY_REGISTERD');
            $this->output();
        }
    }

    //======================================================//

    private function login()
    {
        $User       = $this->User;
        $Cognito    = $this->Cognito;
        $req_params = $this->req_params;

        $user_data  = $User->get_auth_data($req_params['identity_id']);

        if (!empty($user_data)) {
            $req_params = $user_data[0];
            $req_params['profile_img'] = Model_V3_Transcode::decode_profile_img($req_params['profile_img']);

        } else {
            $this->status = Model_V3_Status::getStatus('ERROR_IDENTITY_ID_NOT_REGISTERD');
            $this->output();
        }

        self::set_session($req_params['user_id']);
        $req_params['cognito_token'] = $Cognito->get_token();

        $this->req_params = $req_params;
    }


    private function create_user()
    {
        $User       = $this->User;
        $Device     = $this->Device;
        $Cognito    = $this->Cognito;
        $Sns        = $this->Sns;
        $req_params = $this->req_params;

        $req_params['user_id'] = $User->get_next_user_id();
        self::set_session($req_params['user_id']);

        $result = $Cognito->get_data();
        $req_params['identity_id']      = $result['IdentityId'];
        $req_params['cognito_token']    = $result['Token'];


        $req_params['endpoint_arn'] = $Sns->set_device($req_params);
        $req_params['profile_img']  = self::get_rand_profile_img();

        $User   ->set_data($req_params);
        $Device ->set_data($req_params);

        $req_params['badge_num']   = 0;
        $req_params['profile_img'] = Model_V3_Transcode::decode_profile_img($req_params['profile_img']);

        $this->req_params = $req_params;
    }


    private function update_device()
    {
        //外部処理 Link:Controller/bgp/update_device
        $req_params = $this->req_params;
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,
            'http://localhost/v3/bgp/update_device/'
            .'?user_id='     . "$req_params[user_id]"
            .'&os='          . "$req_params[os]"
            .'&ver='         . "$req_params[ver]"
            .'&model='       . "$req_params[model]"
            .'&register_id=' . "$req_params[register_id]"
        );

        curl_exec($ch);
        curl_close($ch);
    }
}