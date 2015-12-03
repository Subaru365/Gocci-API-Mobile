<?php
/**
 * Set Class. This class request session.
 *
 * @package    Gocci-Mobile
 * @version    3.0 (2015/11/25)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @license    MIT License
 * @copyright  2015 Inase,inc.
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

class Controller_V3_Set extends Controller_V3_Gate
{
	public function before()
	{
		parent::before();
	}


	public function action_password()
	{
		//Input is $password
		$user 	= Model_V3_User::getInstance();
		$result = $user->setPassword($this->req_params['password']);

		$this->req_params = $result;
		$this->output_success();
	}


	public function action_sns_link()
	{
		//$input is provider, token
		$user 	= Model_V3_User::getInstance();
		$result = $user->setSnsLink($this->req_params);

		$this->output_success();
	}


	public function action_sns_unlink()
	{
		//input is provider, token
		$user 	= Model_V3_User::getInstance();
		$result = $user->setSnsUnLink($this->req_params);

		$this->output_success();
	}


	public function action_gochi()
	{
		//Input post_id
		$gochi = Model_V3_Db_Gochi::getInstance();
		$post  = Model_V3_Db_Post::getInstance();

		$result			= $gochi->setGochi($this->req_params['post_id']);
		$post_user_id 	= $post->getPostUserId($this->req_params['post_id']);

		if (session::get('user_id') != $post_user_id) {
		//通知外部処理
			$this->bgpNoticeGochi($this->req_params['post_id'], $post_user_id);
		}

		$this->req_params['gochi_id'] = $result;
		$this->output_success();
	}


	public function action_comment()
	{
		//Input post_id, comment, re_user_id
		$comment = Model_V3_Db_Comment::getInstance();
		$post    = Model_V3_Db_Post::getInstance();

		$comment_id = $comment->setComment($this->req_params);

		if (empty($this->req_params['re_user_id'])) {

		} else {
			$re = Model_V3_Db_Re::getInstance();
			$re->setRe($comment_id, $this->req_params['re_user_id']);
		}

		$post_user_id = $post->getPostUserId($this->req_params['post_id']);

		if (session::get('user_id') != $post_user_id) {
			//通知外部処理

			if (!empty($this->req_params['re_user_id'])) {
				//re_user あり
				$this->bgpNoticeComment($this->req_params, $post_user_id);
			} else {
				//re_user なし
				$this->req_params['re_user_id'] = '';
				$this->bgpNoticeComment($this->req_params, $post_user_id);
			}

		} else if (!empty($this->req_params['re_user_id'])) {
			$this->bgpNoticeComment($this->req_params);

		} else {
			//通知なし
		}

		$this->req_params['comment_id'] = $comment_id;
		$this->output_success();
	}


	public function action_follow()
	{
		//Input target_user_id
		$follow = Model_V3_Db_Follow::getInstance();
		$result = $follow->setFollow($this->req_params['user_id']);

		$this->bgpNoticeFollow($this->req_params['user_id']);

		$this->req_params['follow_id'] = $result;
		$this->output_success();
	}


	public function action_unfollow()
	{
		//Input target_user_id
		$follow = Model_V3_Db_Follow::getInstance();
		$result = $follow->setUnFollow($this->req_params['user_id']);

		$this->bgpNoticeFollow($this->req_params['user_id']);

		$this->output_success();
	}


	public function action_want()
	{
		//Input rest_id
		$want = Model_V3_Db_Want::getInstance();
		$result = $want->setWant($this->req_params['rest_id']);

		$this->req_params['want_id'] = $result;
		$this->output_success();
	}


	public function action_unwant()
	{
		//Input rest_id
		$want = Model_V3_Db_Want::getInstance();
		$result = $want->setUnWant($this->req_params['rest_id']);

		$this->output_success();
	}


	public function action_post()
	{
		//Input rest_id, movie_name, category_id, value, memo, cheer_flag
		$post = Model_V3_Db_Post::getInstance();

		$this->req_params = Model_V3_Transcode::encodePostName($this->req_params);
		$post_id = $post->setPostData($this->req_params);
		$hash_id = Model_V3_Hash::postIdHash($post_id);
		$post->setHashId($post_id, $hash_id);

		$this->req_params['post_id'] = $post_id;
		$this->output_success();
	}


	public function action_post_block()
	{
		//Input post_id
		$block = Model_V3_Db_Block::getInstance();
		$result = $block->setBlock($this->req_params['post_id']);

		$this->output_success();
	}


	public function action_post_delete()
	{
		//Input post_id
		$post = Model_V3_Db_Post::getInstance();
		$result = $post->postDelete($this->req_params['post_id']);

		$this->output_success();
	}


	public function action_username()
	{
		//Input username
		$user = Model_V3_Db_User::getInstance();
		if ($user->check_name($this->req_params['username'])) {
			//username 登録済み
			$this->status = Model_V3_Status::getStatus('ERROR_USERNAME_ALREADY_REGISTERD');
            $this->output();
		} else {
			$result = $user->setMyName($this->req_params['username']);
		}

		$this->output_success();
	}


	public function action_profile_img()
	{
		//Input profile_img
		$user = Model_V3_Db_User::getInstance();
		$result = $user->setProfileImg($this->req_params['profile_img']);

		$this->req_params = $result;
		$this->output_success();
	}


	//Feedback
	public function action_feedback()
	{
		//Input feedback
		$feedback = Model_V3_Db_Feedback::getInstance();
		$result = $feedback->setFeedback($this->req_params['feedback']);

		$this->req_params['feedback_id'] = $result;
		$this->output_success();
	}


	//RestAdd
	public function action_rest()
	{
		//Input restname, lat, lon
		$rest = Model_V3_Db_Restaurant::getInstance();
		$result = $rest->setRestData($this->req_params);

		$this->req_params['rest_id'] = $result;
		$this->output_success();
	}

	//======================================================//



	private function bgpNoticeGochi($post_id, $post_user_id)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,
            'http://localhost/v3/bgp/notice_gochi/'
            .'?user_id='		. session::get('user_id')
            .'&post_id='    	. "$post_id"
            .'&post_user_id=' 	. "$post_user_id"
        );
        curl_exec($ch);
        curl_close($ch);
    }

    private function bgpNoticeComment($params, $post_user_id)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,
            'http://localhost/v3/bgp/notice_comment/'
            .'?user_id='		. session::get('user_id')
            .'&post_id='     	. "$params[post_id]"
            .'&post_user_id=' 	. "$post_user_id"
            .'&re_user_id='	 	. "$params[re_user_id]"
        );
        curl_exec($ch);
        curl_close($ch);
    }

    private function bgpNoticeFollow($follow_user_id)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,
            'http://localhost/v3/bgp/notice_follow/'
            .'?user_id='		. session::get('user_id')
            .'&follow_user_id=' . "$follow_user_id"
        );
        curl_exec($ch);
        curl_close($ch);
    }
}