<?php
/**
 * Get  Class. Request SignUp, LogIn.
 *
 * @package    Gocci-Mobile
 * @version    3.0 (2015/11/09)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @license    MIT License
 * @copyright  2015 Inase,inc.
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

class Controller_V3_Get extends Controller_V3_Gate
{
	/**
	 * @var Instance $Post
	 */
	protected $post;

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
		$post = Model_V3_Db_Post::getInstance();

		$post->setPosition($this->req_params['lon'], $this->req_params['lat']);
		$this->req_params = $post->getNearPost();

		$this->output_success();
	}

	public function action_timeline()
    {
    	//$option is [call, order_id, category_id, value_id, lon, lat]
        $option		= self::get_input();

		$post_data  = Model_V2_Router::timeline($option);

	   	$this->output_success($post_data);
	}

	//Followline
	public function action_followline()
	{
		//$option is [call, order_id, category_id, value_id, lon, lat]
        $potion		= self::get_input();

		$post_data	= Model_V2_Router::followline($option);

	   	$this->output_success($post_data);
	}


	//Comment Page
    public function action_comment()
    {
    	//$option is [post_id]
    	$post_id 		= self::get_input();

		$post_data   	= Model_V2_Router::comment_post($post_id);
	   	$comment_data   = Model_V2_Router::comment($post_id);

	   	$data = array(
	   		"post" 		=> $post_data,
	   		"comments" 	=> $comment_data
	   	);

	   	$this->output_success($data);
	}


	//Restaurant Page
	public function action_rest()
    {
    	//$option is [rest_id]
    	$rest_id	= self::get_input();

		$rest_data 	= Model_V2_Router::rest($rest_id);
		$post_data 	= Model_V2_Router::rest_post($rest_id);

	   	$data = array(
	   		"restaurant"	=> $rest_data,
	   		"posts" 		=> $post_data
	   	);

	   	$this->output_json($data);
	}


	//User Page
	public function action_user()
	{
		//$option is [target_user_id]
    	$option		= self::get_input();

		$user_data  = Model_V2_Router::user($target_user_id);
        $post_data  = Model_V2_Router::user_post($option);

	   	$data = array(
	   		"user"	=> $user_data,
	   		"posts" => $post_data
	   	);

	   	$this->output_json($data);
	}


	//Notice Page
	public function action_notice()
    {
    	$notice_data = Model_V2_Router::notice();

	   	$this->output_json($notice_data);
	}


	//Follow
	public function action_follow()
	{
		$target_user_id	= Input::get('target_user_id');

		$follow_data	= Model_V2_Router::follow_list($target_user_id);

	   	$this->output_json($data);
	}


	//Follower List
	public function action_follower()
	{
		$target_user_id = Input::get('target_user_id');

		$follow_data	= Model_V2_Router::follower_list($target_user_id);

	   	$this->output_json($data);
	}


	//行きたい登録リスト
	public function action_want()
	{
		$target_user_id = Input::get('target_user_id');

		$want_data		= Model_V2_Router::want_list($target_user_id);

	   	$this->output_json($data);
	}


	//応援店舗リスト
	public function action_cheer()
	{
		$target_user_id = Input::get('target_user_id');

		$cheer_data     = Model_V2_Router::cheer_list($target_user_id);

	   	$this->output_json($cheer_data);
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
		$req_params = $post->getHeatmap();
		$this->output_success();
	}


	//応援ユーザーリスト
	// public function action_rest_cheer()
	// {
	// 	$rest_id = Input::get('rest_id');

	// 	$data = Model_Post::get_rest_cheer($rest_id);

	// 	$num = count($data);

	// 	for ($i=0; $i < $num; $i++) {
	// 		$data[$i]['follow_flag'] = Model_Follow::get_flag($data[$i]['user_id']);
	// 	}

	//    	$this->output_json($data);
	// }


	// //Near
	// public function action_near()
	// {
	// 	$lon 	= Input::get('lon');
	// 	$lat 	= Input::get('lat');

	// 	$data 	= Model_Restaurant::get_near($lon, $lat);

	//    	$this->output_json($data);
	// }

}

