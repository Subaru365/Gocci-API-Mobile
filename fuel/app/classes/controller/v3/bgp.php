<?php
/**
 * Background Process Class. Access to localhost
 *
 * @package    Gocci-Mobile
 * @version    3.0 (2015/10/30)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @license    MIT License
 * @copyright  2015 Inase,inc.
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

class Controller_V3_Bgp extends Controller
{
    private $Device;

    private $Sns;

    public function before()
    {
        if ($_SERVER['REMOTE_ADDR'] !== '127.0.0.1') {
            error_log('access denied. @Bgp');
            exit;
        }
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

    public function action_update_device()
    {
        $params = Input::get();

        $this->Device = new Model_V3_Db_Device();
        $this->Sns    = new Model_V3_Aws_Sns();

        $old_endpoint_arn = $this->Device->get_arn($params['user_id']);
        $this->Sns->delete_data($old_endpoint_arn);

        $params['endpoint_arn'] = $this->Sns->set_device($params);

        $this->Device->update_data($params);
    }
}