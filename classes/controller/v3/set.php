<?php
/**
 * Set Class. This class request session.
 *
 * @package    Gocci-Mobile
 * @version    3.0.1 (2015/12/23)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @copyright  (C) 2015 Akira Murata
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

class Controller_V3_Set extends Controller_V3_Gate
{
	public function before()
	{
		parent::before();
	}


	public function action_device()
	{
		//$req_params is [os, ver, model, device_token]
		$device = Model_V3_Device::getInstance();
		$device->updateDevice($this->req_params);

        $this->outputSuccess();
	}


	public function action_password()
	{
		//Input is $password
		$user 	= Model_V3_User::getInstance();
		$result = $user->setPassword($this->req_params['password']);

		$this->outputSuccess();
	}


	public function action_sns_link()
	{
		//$input is provider, token
		$user 	= Model_V3_User::getInstance();
		$result = $user->setSnsLink($this->req_params);

		$this->outputSuccess();
	}


	public function action_gochi()
	{
		//Input post_id
		$gochi  = Model_V3_Db_Gochi::getInstance();
		$post   = Model_V3_Db_Post::getInstance();
		$params = $this->req_params;

		$result	= $gochi->setGochi($params['post_id']);
		$params['post_user_id'] = $post->getPostUserId($params['post_id']);

		if (session::get('user_id') != $params['post_user_id']) {
			$notice = Model_V3_Notice::getInstance();
			$notice->setGochi($params);
		}

		$this->outputSuccess();
	}


	public function action_comment()
	{
		//Input post_id, comment, re_user_id
		$comment 	= Model_V3_Db_Comment::getInstance();
		$post 		= Model_V3_Db_Post::getInstance();
		$params 	= $this->req_params;

		$comment_id = $comment->setComment($params);

		if (!empty($params['re_user_id'])) {
			$re = Model_V3_Db_Re::getInstance();
			$re->setRe($comment_id, $params['re_user_id']);
		}

		$params['post_user_id'] = $post->getPostUserId($params['post_id']);

		if (session::get('user_id') !== $params['post_user_id'] || !empty($params['re_user_id'])) {
			$notice = Model_V3_Notice::getInstance();
			$notice->setComment($params);
		}

		$this->outputSuccess();
	}


	public function action_follow()
	{
		//Input user_id
		$follow = Model_V3_Db_Follow::getInstance();
		$notice = Model_V3_Notice::getInstance();
		$result = $follow->setFollow($this->req_params['user_id']);

		$notice->setFollow($this->req_params['user_id']);

		$this->outputSuccess();
	}


	public function action_want()
	{
		//Input rest_id
		$want = Model_V3_Db_Want::getInstance();
		$result = $want->setWant($this->req_params['rest_id']);

		$this->outputSuccess();
	}


	public function action_post()
	{
		//Input rest_id, movie_name, category_id, value, memo, cheer_flag
		$post = Model_V3_Db_Post::getInstance();

		$this->req_params = Model_V3_Transcode::encodePostName($this->req_params);
		$post_id = $post->setPostData($this->req_params);
		$hash_id = Model_V3_Hash::postIdHash($post_id);
		$post->setHashId($post_id, $hash_id);
		$this->res_params['post_id'] = $post_id;

		$this->outputSuccess();
	}


	public function action_post_block()
	{
		//Input post_id
		$block = Model_V3_Db_Block::getInstance();
		$result = $block->setBlock($this->req_params['post_id']);

		$this->outputSuccess();
	}


	public function action_username()
	{
		//Input username
		$user = Model_V3_Db_User::getInstance();
		if ($user->getIdForName($this->req_params['username'])) {
			$param = Model_V3_Param::getInstance();
			$param->set_set_username_ERROR_USERNAME_ALREADY_REGISTERD();
            $this->output();
		} else {
			$result = $user->setMyName($this->req_params['username']);
		}

		$this->outputSuccess();
	}


	public function action_profile_img()
	{
		//Input profile_img
		$user = Model_V3_Db_User::getInstance();
		$result = $user->setMyProfileImg($this->req_params['profile_img']);

		$this->res_params['profile_img'] = Model_V3_Transcode::decode_profile_img($this->req_params['profile_img']);
		$this->outputSuccess();
	}


	//Feedback
	public function action_feedback()
	{
		//Input feedback
		$feedback = Model_V3_Db_Feedback::getInstance();
		$result = $feedback->setFeedback($this->req_params['feedback']);

		$this->outputSuccess();
	}


	//RestAdd
	public function action_rest()
	{
		//Input restname, lat, lon
		$rest = Model_V3_Db_Restaurant::getInstance();
		$result = $rest->setRestData($this->req_params);

		$this->res_params['rest_id'] = $result;
		$this->outputSuccess();
	}
}