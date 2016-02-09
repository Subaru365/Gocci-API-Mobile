<?php
/**
 * Get  Class. Request SignUp, LogIn.
 *
 * @package    Gocci-Mobile
 * @version    4.2.0 (2016/1/26)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @copyright  (C) 2016 Akira Murata
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

class Controller_V4_Get extends Controller_V4_Gate
{
	/**
	 * @var Instance $User
	 */
	protected $User;

	public function before()
	{
		parent::before();
	}


	public function action_nearline()
    {
		// $req_params is lon, lat(, call, category_id, value_id)
		$post = Model_V4_Post::getInstance();

		$this->res_params['posts'] = $post->getNearline($this->req_params);

		if (empty($this->res_params['posts'])) {
			$param = Model_V4_Param::getInstance();
			$param->setGlobalCode_SUCCESS(array('posts'=>array()));
			$this->output();
		}

		$this->outputSuccess();
	}


	public function action_followline()
	{
		//$option is [call, order_id, category_id, value_id, lon, lat]
		$post 	 = Model_V4_Post::getInstance();
		$follow  = Model_V4_Db_Follow::getInstance();

		$this->req_params['follow_user_id'] = $follow->getFollowId();

		if (empty($this->req_params['follow_user_id'])) {
			$param = Model_V4_Param::getInstance();
			$param->setGlobalCode_SUCCESS(array('posts'=>array()));
			$this->output();
		}

		$this->res_params['posts'] = $post->getFollowline($this->req_params);
	   	$this->outputSuccess();
	}


	public function action_gochiline()
	{
		//$option is [call, order_id, category_id, value_id, lon, lat]
		$post 	 = Model_V4_Post::getInstance();
		$gochi   = Model_V4_Db_Gochi::getInstance();

		$this->req_params['gochi_post_id'] = $gochi->getPostId();

		if (empty($this->req_params['gochi_post_id'])) {
			$param = Model_V4_Param::getInstance();
			$param->setGlobalCode_SUCCESS(array('posts'=>array()));
			$this->output();
		}

		$this->res_params['posts'] = $post->getGochiline($this->req_params);
	   	$this->outputSuccess();
	}


	public function action_timeline()
    {
    	//$option is [call, order_id, category_id, value_id, lon, lat]
    	$post = Model_V4_Post::getInstance();

		$this->res_params['posts'] = $post->getTimeline($this->req_params);
	   	$this->outputSuccess();
	}


	public function action_post()
	{
		//input post_id
		$post = Model_V4_Post::getInstance();

		$this->res_params = $post->getPost($this->req_params['post_id']);
		$this->outputSuccess();
	}


	public function action_comment()
	{
		//$option is [post_id]
		$post 		= Model_V4_Post::getInstance();
		$comment 	= Model_V4_Comment::getInstance();

		$memo_data 		= $post->getMemo($this->req_params['post_id']);
		$comment_data 	= $comment->getComment($this->req_params['post_id']);

		$this->res_params = array(
			"memo" 		=> $memo_data,
			"comments" 	=> $comment_data
		);

		$this->outputSuccess();
	}


	public function action_rest()
	{
		//$option is [rest_id]
		$rest = Model_V4_Restaurant::getInstance();
		$post = Model_V4_Post::getInstance();

		$rest_data 	= $rest->getRestData($this->req_params['rest_id']);
		$post_data 	= $post->getRestPost($this->req_params['rest_id']);

		$this->res_params = array(
			"rest" 	=> $rest_data,
			"posts" => $post_data
		);

		$this->outputSuccess();
	}


	public function action_user()
	{
		//$option is [target_user_id]
		$user = Model_V4_User::getInstance();
    	$post = Model_V4_Post::getInstance();

		$user_data  = $user->getProfile($this->req_params['user_id']);
        $post_data 	= $post->getUserPost($this->req_params['user_id']);

	   	$this->res_params = array(
	   		"user"	=> $user_data,
	   		"posts" => $post_data,
	   	);

	   	$this->outputSuccess();
	}


	public function action_notice()
    {
    	$notice = Model_V4_Notice::getInstance();
    	$data = $notice->getNotice();

    	if (empty($data)) {
    		$param = Model_V4_Param::getInstance();
    		$param->setGlobalCode_SUCCESS(array('notices'=>array()));
			$this->output();
    	}

    	$user = Model_V4_Db_User::getInstance();
    	$user->resetBadge();

    	$this->res_params['notices'] = $data;
	   	$this->outputSuccess();
	}


	public function action_follow()
	{
		//$option is [user_id]
		$user = Model_V4_User::getInstance();

		$data = $user->getFollowlist($this->req_params['user_id']);

		if (empty($data)) {
			$param = Model_V4_Param::getInstance();
			$param->setGlobalCode_SUCCESS(array('users'=>array()));
			$this->output();
		}

		$this->res_params['users'] = $data;
	   	$this->outputSuccess();
	}


	public function action_follower()
	{
		//$option is [user_id]
		$user = Model_V4_User::getInstance();

		$data = $user->getFollowerlist($this->req_params['user_id']);

		if (empty($data)) {
			$param = Model_V4_Param::getInstance();
			$param->setGlobalCode_SUCCESS(array('users'=>array()));
			$this->output();
		}

		$this->res_params['users'] = $data;
	   	$this->outputSuccess();
	}


	public function action_user_cheer()
	{
		//$option is [user_id]
		$post = Model_V4_Db_Post::getInstance();

		$data = $post->getUserCheer($this->req_params['user_id']);

		if (empty($data)) {
			$param = Model_V4_Param::getInstance();
			$param->setGlobalCode_SUCCESS(array('rests'=>array()));
			$this->output();
		}

		$this->res_params['rests'] = $data;
	   	$this->outputSuccess();
	}


	public function action_username()
	{
		//Input username
		$user = Model_V4_User::getInstance();
		$this->res_params['users'] = $user->getUsername($this->req_params['username']);
	   	$this->outputSuccess();
	}


	public function action_heatmap()
	{
		$post  = Model_V4_Db_Post::getInstance();
		$rests = $post->getPositionPost();

		$num = count($rests);
		for ($i=0; $i < $num; $i++) {
			$this->res_params['rests'][$i] = Model_V4_Transcode::decodeLonLat($rests[$i]);
		}
		$this->outputSuccess();
	}


	public function action_near()
	{
		$rest = Model_V4_Db_Restaurant::getInstance();
		$this->res_params['rests'] = $rest->getNearData($this->req_params['lon'], $this->req_params['lat']);
		$this->outputSuccess();
	}


	public function action_follower_rank()
	{
		//input OPT page
		$user = Model_V4_User::getInstance();
		$this->res_params['users'] = $user->getFollowerRank($this->req_params['page']);

		if (empty($this->res_params)) {
			$param = Model_V4_Param::getInstance();
			$param->setGlobalCode_SUCCESS(array('users'=>array()));
			$this->output();
		}
		$this->outputSuccess();
	}
}

