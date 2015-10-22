<?php
/**
 * API second gate. Check Session.
 *
 * @package    Gocci-Mobile
 * @version    3.0 (2015/10/21)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @license    MIT License
 * @copyright  2015 Inase,inc.
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

class Controller_V3_Gate extends Controller_V3_Input
{
	public function before()
	{
		if(session::get()) {
            $status         = new Model_V3_Status();
            $this->status   = $status->ERROR_SESSION_EXPIRED();

        } else {
            parent::before();
        }
	}
}