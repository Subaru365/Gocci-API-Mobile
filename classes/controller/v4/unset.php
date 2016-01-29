<?php
/**
 * UnSet Class. This class request session.
 *
 * @package    Gocci-Mobile
 * @version    4.2.0 (2016/1/28)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @copyright  (C) 2016 Akira Murata
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

class Controller_V4_Unset extends Controller_V4_Gate
{
	public function before()
	{
		parent::before();
	}


	public function action_device()
	{
		//$req_params is [device_token]
		$device = Model_V4_Db_Device::getInstance();
		$result = $device->getEndpointArn(session::get('user_id'));

		if (!empty($result)) {
			$endpoint_arn = $result[0]['endpoint_arn'];
			$device->deleteDevice(session::get('user_id'));

			$sns = Model_V4_Aws_Sns::getInstance();
			$sns->deleteSns($endpoint_arn);
		}

        $this->outputSuccess();
	}


	public function action_sns_link()
	{
		//input is provider, token
		$user 	= Model_V4_User::getInstance();
		$result = $user->setSnsUnLink($this->req_params);

		$this->outputSuccess();
	}


	public function action_gochi()
	{
		//Input post_id
		$gochi = Model_V4_Db_Gochi::getInstance();
		$result = $gochi->setUnGochi($this->req_params['post_id']);

		$this->outputSuccess();
	}


	public function action_comment()
	{
		//Input post_id
		$re 	 = Model_V4_Db_Re::getInstance();
		$comment = Model_V4_Db_Comment::getInstance();
		$result  = $re->unsetRe($this->req_params['comment_id']);
		$result  = $comment->setUnComment($this->req_params['comment_id']);

		$this->outputSuccess();
	}


	public function action_follow()
	{
		//Input target_user_id
		$follow = Model_V4_Db_Follow::getInstance();
		$result = $follow->setUnFollow($this->req_params['user_id']);

		$this->outputSuccess();
	}


	public function action_post()
	{
		//Input post_id
		$post = Model_V4_Db_Post::getInstance();
		$result = $post->deletePost($this->req_params['post_id']);

		$this->outputSuccess();
	}
}