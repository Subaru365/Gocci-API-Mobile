<?php
/**
 * UnSet Class. This class request session.
 *
 * @package    Gocci-Mobile
 * @version    3.0.00 (2015/12/18)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @copyright  (C) 2015 Akira Murata
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

class Controller_V3_Unset extends Controller_V3_Gate
{
	public function before()
	{
		parent::before();
	}


	public function action_device()
	{
		//$req_params is [device_token]
		$device = Model_V3_Db_Device::getInstance();
		$result = $device->getEndpointArn(session::get('user_id'));

		if (!empty($result)) {
			$endpoint_arn = $result[0]['endpoint_arn'];
			$device->deleteDevice(session::get('user_id'));

			$sns = Model_V3_Aws_Sns::getInstance();
			$sns->deleteSns($endpoint_arn);
		}

        $this->outputSuccess();
	}


	public function action_sns_link()
	{
		//input is provider, token
		$user 	= Model_V3_User::getInstance();
		$result = $user->setSnsUnLink($this->req_params);

		$this->outputSuccess();
	}


	public function action_follow()
	{
		//Input target_user_id
		$follow = Model_V3_Db_Follow::getInstance();
		$result = $follow->setUnFollow($this->req_params['user_id']);

		$this->outputSuccess();
	}


	public function action_want()
	{
		//Input rest_id
		$want = Model_V3_Db_Want::getInstance();
		$result = $want->setUnWant($this->req_params['rest_id']);

		$this->outputSuccess();
	}


	public function action_post()
	{
		//Input post_id
		$post = Model_V3_Db_Post::getInstance();
		$result = $post->postDelete($this->req_params['post_id']);

		$this->outputSuccess();
	}
}