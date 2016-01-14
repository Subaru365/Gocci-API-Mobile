<?php
/**
 * API second gate. Check Session.
 *
 * @package    Gocci-Mobile
 * @version    4.0.0 (2016/1/14)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @copyright  (C) 2016 Akira Murata
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

class Controller_V4_Gate extends Controller_V4_Public
{
	public function before()
	{
		if(session::get('user_id')) {
            parent::before();

        } else {
        	$param = Model_V4_Param::getInstance();
            $param->setGlobalCode_ERROR_SESSION_EXPIRED();
            $this->output();
        }
	}
}