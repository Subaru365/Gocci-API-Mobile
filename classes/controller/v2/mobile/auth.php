<?php
/**
 * Auth api
 *
 * // $time_start = microtime(true);
 * // debug
 * // $timelimit = microtime(true) - $time_start;
 * // echo '格納完了：' . $timelimit . ' seconds\r\n';
 *
 */

class Controller_V2_Mobile_Auth extends Controller
{
    public function before()
    {
        $input_data = array_merge(Input::get(), Input::post());
        Model_V2_Validation::check($input_data);
        return $input_data;
    }


    public function standby()
    {
        $movie = Model_V2_Router::get_movie();

        Controller_V2_Mobile_Base::output_success($movie);
    }


    public function action_signup()
    {
        //Input $user_data is [username, os, model, register_id]

        $user_data = Model_v2_Router::create_user($user_data);

        Controller_V2_Mobile_Base::output_success($user_data);
    }


    public function action_login()
    {
        //Input $user_data is [identity_id]

        $user_data = Model_V2_Router::login($user_data['identity_id']);

        Controller_V2_Mobile_Base::output_success($user_data);
    }


    public function action_sns_login()
    {
        //Input $user_data is [identity_id, os, model, register_id]

        $user_data = Model_V2_Router::login($user_data['identity_id']);

        Controller_V2_Mobile_Base::output_success($user_data);
    }


    public function action_pass_login()
    {
        //Input $user_data is [username, pass, os, model, register_id]

        $user_data = Model_V2_Router::pass_login($user_data['identity_id']);

        Controller_V2_Mobile_Base::output_success($user_data);
    }


    // public function action_device_refresh()
    // {
    //     //Input $user_data is [$register_id]
    //     $user_data = self::get_input();

    //     $old_endpoint_arn = Model_Device::get_arn($user_id);
    //     Model_Sns::delete_endpoint($old_endpoint_arn);

    //     $new_endpoint_arn = Model_Sns::post_endpoint($user_id, $register_id, $os);
    //     Model_Device::update_data($user_id, $os, $model, $register_id, $new_endpoint_arn);

    // }
}