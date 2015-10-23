<?php
/**
 * Authentication Class. Request SignUp, LogIn.
 *
 * @package    Gocci-Mobile
 * @version    3.0 (2015/10/21)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @license    MIT License
 * @copyright  2015 Inase,inc.
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

class Controller_V3_Auth extends Controller_V3_Input
{
    public function action_signup()
    {
        //$parameter is [name, os, model, reg_id]

        $this->parameter = Model_V3_Router::create_user($this->parameter);

        Controller_V3_Mobile_Base::output_success($this->parameter);

                $this->overlap_username($user_data['username']);
        $this->overlap_register_id($user_data['register_id']);
    }


    public function action_login()
    {
        //Input $this->parameter is [identity_id]

        $this->parameter = Model_V3_Router::login($this->parameter['identity_id']);

        Controller_V3_Mobile_Base::output_success($this->parameter);

        $this->verify_identity_id(['identity_id']);
    }


    public function action_sns_login()
    {
        //Input $this->parameter is [identity_id, os, model, register_id]

        $this->parameter = Model_V3_Router::login($this->parameter['identity_id']);

        Controller_V3_Mobile_Base::output_success($this->parameter);


        $this->verify_identity_id(['identity_id']);
        $this->overlap_register_id(['register_id']);
    }


    public function action_pass_login()
    {
        //Input $this->parameter is [username, pass, os, model, register_id]

        $this->parameter = Model_V3_Router::pass_login($this->parameter['identity_id']);

        Controller_V3_Mobile_Base::output_success($this->parameter);

        $this->verify_password($user_data['username'], $user_data['pass']);
    }


    // public function action_device_refresh()
    // {
    //     //Input $this->parameter is [$register_id]
    //     $this->parameter = self::get_input();

    //     $old_endpoint_arn = Model_Device::get_arn($user_id);
    //     Model_Sns::delete_endpoint($old_endpoint_arn);

    //     $new_endpoint_arn = Model_Sns::post_endpoint($user_id, $register_id, $os);
    //     Model_Device::update_data($user_id, $os, $model, $register_id, $new_endpoint_arn);

    // }
}