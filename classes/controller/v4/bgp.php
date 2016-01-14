<?php
/**
 * Background Process Class. Access to localhost
 *
 * @package    Gocci-Mobile
 * @version    4.0.0 (2016/1/14)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @copyright  (C) 2016 Akira Murata
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

class Controller_V4_Bgp extends Controller
{

    //Post有効化
    public function action_post_publish()
    {
        $post   = Model_V4_Db_Post::getInstance();
        $notice = Model_V4_Notice::getInstance();

        $user_id = Input::get('user_id');
        $movie   = Input::get('movie');

        $post->enablePost($movie);
        $notice->pushPostComplete($user_id);

        echo 'SUCCESS!';
    }
}