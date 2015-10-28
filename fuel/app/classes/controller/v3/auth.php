<?php
/**
 * Authentication Class. Request SignUp, LogIn.
 *
 * @package    Gocci-Mobile
 * @version    3.0 (2015/10/27)
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


    public function before()
    {
        parent::before();

        $this->Cognito = new Model_V3_Aws_Cognito();
        $this->User    = new Model_V3_Db_User();
    }

    public function action_signup()
    {
        //$req_params is [name, os, model, reg_id]

        $this->req_params = Model_V3_Router::create_user($this->req_params);

        Controller_V3_Mobile_Base::output_success($this->req_params);

                $this->overlap_username($user_data['username']);
        $this->overlap_register_id($user_data['register_id']);
    }

    public function action_login()
    {
        //Input $this->req_params is [identity_id]
        //$this->verify_identity_id();
        // print_r($this->req_params);
        // exit;

        $this->login();

        $this->output_success();
    }


    public function action_sns_login()
    {
        //Input $this->req_params is [identity_id, os, model, register_id]

        $this->req_params = Model_V3_Router::login($this->req_params['identity_id']);

        Controller_V3_Mobile_Base::output_success($this->req_params);


        $this->verify_identity_id(['identity_id']);
        $this->overlap_register_id(['register_id']);
    }


    public function action_pass_login()
    {
        //Input $this->req_params is [username, pass, os, model, register_id]

        $this->req_params = Model_V3_Router::pass_login($this->req_params['identity_id']);

        Controller_V3_Mobile_Base::output_success($this->req_params);

        $this->verify_password($user_data['username'], $user_data['pass']);
    }


    // public function action_device_refresh()
    // {
    //     //Input $this->req_params is [$register_id]
    //     $this->req_params = self::get_input();

    //     $old_endpoint_arn = Model_Device::get_arn($user_id);
    //     Model_Sns::delete_endpoint($old_endpoint_arn);

    //     $new_endpoint_arn = Model_Sns::post_endpoint($user_id, $register_id, $os);
    //     Model_Device::update_data($user_id, $os, $model, $register_id, $new_endpoint_arn);

    // }




    //======================================================//
    // Functions
    //======================================================//

    private static function set_session($user_id)
    {
        Session::set('user_id', "$user_id");
    }

    private function login()
    {
        $User           =  $this->User;
        $Cognito        =  $this->Cognito;
        $identity_id    =  $this->req_params['identity_id'];
        $res_params     = &$this->res_params;

        $result         = $User->get_auth_data($identity_id);

        if (!empty($result)) {
            $result[0]['profile_img'] = Model_V3_Transcode::decode_profile_img($result[0]['profile_img']);
            $res_params = $result[0];
        
        } else {
            $this->status = Model_V3_Status::get_status('ERROR_IDENTITY_ID_NOT_REGISTERD');
            $this->output();
        }

        self::set_session($res_params['user_id']);
        $res_params['cognito_token'] = $Cognito->get_token($identity_id);
    }
}