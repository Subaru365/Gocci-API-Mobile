<?php
/**
 * Background Process Class. Access to localhost
 *
 * @package    Gocci-Mobile
 * @version    4.1.0 (2016/1/22)
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

        $movie = Input::get('movie');

        $post->postEnable($movie);
        $notice->pushPostComplete($movie);

        echo 'SUCCESS!';
    }

    public function action_announce()
    {
        $notice = Model_V4_Notice::getInstance();

        $params['alert']   = Input::get('alert');
        $params['message'] = Input::get('message');

        $notice->pushAnnouncement($params);

        echo 'SUCCESS!';
    }
}