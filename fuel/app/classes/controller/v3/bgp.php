<?php
/**
 * Background Process Class. Access to localhost
 *
 * @package    Gocci-Mobile
 * @version    3.0.0 (2015/12/10)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @copyright  (C) 2015 Akira Murata
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

class Controller_V3_Bgp extends Controller
{
    public function before()
    {
        if ($_SERVER['REMOTE_ADDR'] !== '127.0.0.1') {
            error_log('access denied.');
            exit;
        }
    }

    // public function action_update_device()
    // {
    //     $params = Input::get();

    //     $device = Model_V3_Db_Device::getInstance();
    //     $sns    = Model_V3_Aws_Sns::getInstance();

    //     $old_endpoint_arn = $device->get_arn($params['user_id']);
    //     $sns->delete_data($old_endpoint_arn);

    //     $params['endpoint_arn'] = $sns->set_device($params);
    //     $device->updateDevice($params);
    // }

    //input is user_id, post_id, post_user_id
    public function action_notice_gochi()
    {
        $params = Input::get();

        $notice = Model_V3_Notice::getInstance();
        $notice->pushGochi($params);
    }

    //input is user_id, post_id, post_user_id, re_user_id
    public function action_notice_comment()
    {
        $params = Input::get();

        $notice = Model_V3_Notice::getInstance();
        $notice->pushComment($params);
    }

    //input is user_id, follow_user_id
    public function action_notice_follow()
    {
        $params = Input::get();

        $notice = Model_V3_Notice::getInstance();
        $notice->pushFollow($params);
    }

    // //SNS Push
    // public function action_publish()
    // {
    //     $keyword   = Input::get('keyword');
    //     $a_user_id = Input::get('a_user_id');
    //     $p_user_id = Input::get('p_user_id');

    //     $login_flag = Model_User::check_login($p_user_id);

    //     if ($login_flag == '1') {
    //     //ログイン中
    //         Model_Sns::post_message($keyword, $a_user_id, $p_user_id);
    //     }
    // }


    // //Post有効化
    // public function action_post_publish()
    // {
    //     $message = '投稿が完了しました！';

    //     $user_id = Input::get('user_id');
    //     $movie   = Input::get('movie');

    //     Model_Post::post_publish($movie);
    //     Model_Sns::post_publish($user_id, $message);

    //     echo '確認ありがとう！';
    // }


    // //Post有効化
    // public function action_post_reject()
    // {
    //     $message = 'ごめんなさい！料理の動画を撮って下さい';

    //     $user_id = Input::get('user_id');
    //     $movie   = Input::get('movie');

    //     Model_Post::post_reject($movie);
    //     Model_Sns::post_publish($user_id, $message);

    //     echo '拒否しました。確認ありがとう！';
    // }
}