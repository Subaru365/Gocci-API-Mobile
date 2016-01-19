<?php
/**
 * Set Class. This class request session.
 *
 * @package    Gocci-Mobile
 * @version    4.2.0 (2016/1/19)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @copyright  (C) 2016 Akira Murata
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

class Controller_V4_Set extends Controller_V4_Gate
{
	public function before()
	{
		parent::before();
	}


	public function action_device()
	{
		//$req_params is [os, ver, model, device_token]
		$device = Model_V4_Device::getInstance();
		$device->updateDevice($this->req_params);

        $this->outputSuccess();
	}


	public function action_password()
	{
		//Input is $password
		$user 	= Model_V4_User::getInstance();
		$result = $user->setPassword($this->req_params['password']);

		$this->outputSuccess();
	}


	public function action_sns_link()
	{
		//$input is provider, token
		$user 	= Model_V4_User::getInstance();
		$result = $user->setSnsLink($this->req_params);

		$this->outputSuccess();
	}


	public function action_gochi()
	{
		//Input post_id
		$params = $this->req_params;
		$params['a_user_id'] = session::get('user_id');
		$gochi  = Model_V4_Db_Gochi::getInstance();
		$post   = Model_V4_Db_Post::getInstance();

		$result	= $gochi->setGochi($params['post_id']);
		$params['p_user_id'] = $post->getPostUserId($params['post_id']);

		if ($params['a_user_id'] != $params['p_user_id']) {
			$notice = Model_V4_Notice::getInstance();
			$notice->setGochi($params);
		}

		$this->outputSuccess();
	}


	public function action_comment()
	{
		//Input post_id, comment, re_user_id
		$params 	= $this->req_params;
		$params['a_user_id'] = session::get('user_id');
		$comment 	= Model_V4_Db_Comment::getInstance();
		$post 		= Model_V4_Db_Post::getInstance();

		$comment_id = $comment->setComment($params);

		if (!empty($params['re_user_id'])) {
			$re = Model_V4_Db_Re::getInstance();
			$re->setRe($comment_id, $params['re_user_id']);
		}

		$params['p_user_id'] = $post->getPostUserId($params['post_id']);

		if ($params['a_user_id'] != $params['p_user_id'] || !empty($params['re_user_id'])) {
			$notice = Model_V4_Notice::getInstance();
			$notice->setComment($params);
		}

		$this->outputSuccess();
	}


	public function action_comment_block()
	{
		//Input comment_id
		$block = Model_V4_Db_Block::getInstance();
		$result = $block->setCommentBlock($this->req_params['comment_id']);

		$this->outputSuccess();
	}


	public function action_follow()
	{
		//Input user_id
		$params['a_user_id'] = session::get('user_id');
		$params['p_user_id'] = $this->req_params['user_id'];
		$follow = Model_V4_Db_Follow::getInstance();
		$notice = Model_V4_Notice::getInstance();

		$follow->setFollow($params['p_user_id']);
		$notice->setFollow($params);

		$this->outputSuccess();
	}


	public function action_post()
	{
		//Input rest_id, movie_name, category_id, value, memo, cheer_flag
		$post = Model_V4_Db_Post::getInstance();

		$this->req_params = Model_V4_Transcode::encodePostName($this->req_params);
		$post_id = $post->setPostData($this->req_params);

		$hash_id = Model_V4_External::getPostHashId($post_id);
		$post->setHashId($post_id, $hash_id);
		$this->res_params['post_id'] = $post_id;

		$this->outputSuccess();
	}


	public function action_post_block()
	{
		//Input post_id
		$block = Model_V4_Db_Block::getInstance();
		$result = $block->setPostBlock($this->req_params['post_id']);

		$this->outputSuccess();
	}


	public function action_username()
	{
		//Input username
		$user = Model_V4_Db_User::getInstance();
		if ($user->getIdForName($this->req_params['username'])) {
			$param = Model_V4_Param::getInstance();
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
		$user = Model_V4_Db_User::getInstance();
		$result = $user->setMyProfileImg($this->req_params['profile_img']);

		$this->res_params['profile_img'] = Model_V4_Transcode::decode_profile_img($this->req_params['profile_img']);
		$this->outputSuccess();
	}


	public function action_feedback()
	{
		//Input feedback
		$feedback = Model_V4_Db_Feedback::getInstance();
		$result = $feedback->setFeedback($this->req_params['feedback']);

		$this->outputSuccess();
	}


	public function action_rest()
	{
		//Input restname, lat, lon
		$this->req_params['address'] = Model_V4_External::getAddress($this->req_params['lon'], $this->req_params['lat']);

		$rest = Model_V4_Db_Restaurant::getInstance();
		$result = $rest->setRestData($this->req_params);

		$this->res_params['rest_id'] = $result;
		$this->outputSuccess();
	}
}