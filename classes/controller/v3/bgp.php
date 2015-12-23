<?php
/**
 * Background Process Class. Access to localhost
 *
 * @package    Gocci-Mobile
 * @version    3.1.0 (2015/12/23)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @copyright  (C) 2015 Akira Murata
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

class Controller_V3_Bgp extends Controller
{

    //Post有効化
    public function action_post_publish()
    {
        $post   = Model_V3_Db_Post::getInstance();
        $notice = Model_V3_Notice::getInstance();

        $user_id = Input::get('user_id');
        $movie   = Input::get('movie');

        $post->postEnable($movie);
        $notice->pushPostComplete($user_id);

        echo 'SUCCESS!';
    }
}