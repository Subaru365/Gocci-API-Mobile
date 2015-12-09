<?php
/**
 * Get  Class. Request SignUp, LogIn.
 *
 * @package    Gocci-Mobile
 * @version    3.0 (2015/11/24)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @license    MIT License
 * @copyright  2015 Inase,inc.
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

class Controller_V3_Get extends Controller_V3_Gate
{
	/**
	 * @var Instance $User
	 */
	protected $User;

	public function before()
	{
		parent::before();
	}

	public function action_version()
	{

	}


	public function action_nearline()
    {
		// $req_params is lon, lat(, call, category_id, value_id)
		$post = Model_V3_Post::getInstance();

		$this->req_params = $post->getNearline($this->req_params);

		$this->output_success();
	}


	public function action_followline()
	{
		//$option is [call, order_id, category_id, value_id, lon, lat]
		$post 	 = Model_V3_Post::getInstance();
		$follow  = Model_V3_Db_Follow::getInstance();

		$this->req_params['follow_user_id'] = $follow->getFollowId();

		if (empty($follow_user_id)) {
			$this->status = Model_V3_Status::getStatus('SUCCESS');
			$this->output();
		}

		$this->req_params = $post->getFollowline($this->req_params);
	   	$this->output_success();
	}


	public function action_timeline()
    {
    	//$option is [call, order_id, category_id, value_id, lon, lat]
    	$post = Model_V3_Post::getInstance();

		$this->req_params = $post->getTimeline($this->req_params);

	   	$this->output_success();
	}


	public function action_comment()
	{
		//$option is [post_id]
		$post 		= Model_V3_Post::getInstance();
		$comment 	= Model_V3_Comment::getInstance();

		$memo_data 		= $post->getMemo($this->req_params['post_id']);
		$comment_data 	= $comment->getComment($this->req_params['post_id']);

		$this->req_params = array(
			"memo" 		=> $memo_data,
			"comments" 	=> $comment_data
		);

		$this->output_success();
	}


	public function action_rest()
	{
		//$option is [rest_id]
		$rest = Model_V3_Restaurant::getInstance();
		$post = Model_V3_Post::getInstance();

		$rest_data 	= $rest->getRestData($this->req_params['rest_id']);
		$post_data 	= $post->getRestPost($this->req_params['rest_id']);

		$this->req_params = array(
			"rest" 	=> $rest_data,
			"posts" => $post_data
		);

		$this->output_success();
	}


	public function action_user()
	{
		//$option is [target_user_id]
		$user = Model_V3_User::getInstance();
    	$post = Model_V3_Post::getInstance();

		$user_data  = $user->getProfile($this->req_params['user_id']);
        $post_data 	= $post->getUserPost($this->req_params['user_id']);

	   	$this->req_params = array(
	   		"user"	=> $user_data,
	   		"posts" => $post_data,
	   	);

	   	$this->output_success();
	}


	public function action_notice()
    {
    	$notice = Model_V3_Notice::getInstance();
    	$data = $notice->getNotice();

    	if (empty($data)) {
    		$this->status = Model_V3_Status::getStatus('SUCCESS');
			$this->output();
    	}

    	$user = Model_V3_Db_User::getInstance();
    	$user->resetBadge();

    	$this->req_params = $data;
	   	$this->output_success();
	}


	public function action_follow()
	{
		//$option is [user_id]
		$user = Model_V3_User::getInstance();

		$data = $user->getFollowlist($this->req_params['user_id']);

		if (empty($data)) {
			$this->status = Model_V3_Status::getStatus('SUCCESS');
			$this->output();
		}

		$this->req_params = $data;
	   	$this->output_success();
	}


	public function action_follower()
	{
		//$option is [user_id]
		$user = Model_V3_User::getInstance();

		$data = $user->getFollowerlist($this->req_params['user_id']);

		if (empty($data)) {
			$this->status = Model_V3_Status::getStatus('SUCCESS');
			$this->output();
		}

		$this->req_params = $data;
	   	$this->output_success();
	}


	public function action_want()
	{
		//$option is [user_id]
		$want = Model_V3_Db_Want::getInstance();

		$data = $want->getData($this->req_params['user_id']);

		if (empty($data)) {
			$this->status = Model_V3_Status::getStatus('SUCCESS');
			$this->output();
		}

		$this->req_params = $data;
	   	$this->output_success();
	}


	public function action_user_cheer()
	{
		//$option is [user_id]
		$post = Model_V3_Db_Post::getInstance();

		$data = $post->getUserCheer($this->req_params['user_id']);

		if (empty($data)) {
			$this->status = Model_V3_Status::getStatus('SUCCESS');
			$this->output();
		}

		$this->req_params = $data;
	   	$this->output_success();
	}


	public function action_user_search()
	{
		$target_username = Input::get('username');
		$user_id 		 = Model_V2_Router::search_user($target_username);
	   	$this->output_json($data);
	}


	public function action_heatmap()
	{
		$post = Model_V3_Db_Post::getInstance();
		$this->req_params = $post->getPositionPost();
		$this->output_success();
	}


	public function action_near()
	{
		$rest = Model_V3_Db_Restaurant::getInstance();
		$this->req_params = $rest->getNearData($this->req_params['lon'], $this->req_params['lat']);
		$this->output_success();
	}
}

