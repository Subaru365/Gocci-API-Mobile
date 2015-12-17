<?php
/**
 * API second gate. Check Session.
 *
 * @package    Gocci-Mobile
 * @version    3.0 (2015/12/17)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @copyright  (C) 2015 Akira Murata
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

class Controller_V3_Gate extends Controller_V3_Public
{
	public function before()
	{
		if(session::get('user_id')) {
            parent::before();

        } else {
        	$param = Model_V3_Param::getInstance();
            $param->setGlobalCode_ERROR_SESSION_EXPIRED();
            $this->output();
        }
	}
}