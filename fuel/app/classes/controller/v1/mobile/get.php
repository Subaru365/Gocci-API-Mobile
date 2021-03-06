<?php
header('Content-Type: application/json; charset=UTF-8');
error_reporting(-1);
/**
 * Get api
 *
 */

class Controller_V1_Mobile_Get extends Controller_V1_Mobile_Base
{
	//Timeline Page
	public function action_timeline()
    {
    	$sort_key = 'all';
        $user_id  = session::get('user_id');

        $option = array(
        	'call'        => Input::get('call', 0),
        	'order_id'    => Input::get('order_id', 0),
        	'category_id' => Input::get('category_id', 0),
        	'value_id'    => Input::get('value_id', 0),
        	'lon'         => Input::get('lon', 0),
        	'lat'         => Input::get('lat', 0)
        );

		$data = Model_V1_Post::get_data($user_id, $sort_key, 0, $option);
	   	self::output_json($data);
	}


	//Followline
	public function action_followline()
	{
		$sort_key = 'users';
        $user_id  = session::get('user_id');

        $option = array(
        	'call'        => Input::get('call', 0),
        	'order_id'    => Input::get('order_id', 0),
        	'category_id' => Input::get('category_id', 0),
        	'value_id'    => Input::get('value_id', 0),
        	'lon'         => Input::get('lon', 0),
        	'lat'         => Input::get('lat', 0)
        );

		$follow_user_id = Model_V1_Follow::get_follow_id($user_id);
		$data = Model_V1_Post::get_data($user_id, $sort_key, $follow_user_id, $option);

	   	self::output_json($data);
	}


	//Timeline更新 [廃止予定]
	public function action_timeline_next()
	{
		$sort_key = 'all';
        $user_id  = session::get('user_id');

        $option = array(
        	'call'        => Input::get('call', 0),
        	'order_id'    => Input::get('order_id', 0),
        	'category_id' => Input::get('category_id', 0),
        	'value_id'    => Input::get('value_id', 0),
        	'lon'         => Input::get('lon', 0),
        	'lat'         => Input::get('lat', 0)
        );

		$data = Model_V1_Post::get_data($user_id, $sort_key, 0, $option);

	   	self::output_json($data);
	}


	//Popular Page [廃止予定]
	public function action_popular()
    {
    	$sort_key = 'all';
        $user_id  = session::get('user_id');

        $option = array(
        	'call'        => Input::get('call', 0),
        	'order_id'    => Input::get('order_id', 2),
        	'category_id' => Input::get('category_id', 0),
        	'value_id'    => Input::get('value_id', 0),
        	'lon'         => Input::get('lon', 0),
        	'lat'         => Input::get('lat', 0)
        );


		//$post_id = Model_V1_Gochi::get_rank($option['category_id'], $option['value_id'], $option['call']);

		// $num = count($post_id);

		// for ($i=0; $i < $num; $i++) {
		// 	$tmp[$i] = Model_V1_Post::get_data(
		// 		$user_id, $sort_key, $post_id[$i]['post_id'], $option);
		// 	$data[$i] =  $tmp[$i][0];
		// }

        $data = Model_V1_Post::get_data($user_id, $sort_key, 0, $option);
	   	self::output_json($data);
	}


	//Popular Page [廃止予定]
	public function action_popular_next()
    {
    	$sort_key = 'all';
        $user_id  = session::get('user_id');

        $option = array(
        	'call'        => Input::get('call', 0),
        	'order_id'    => Input::get('order_id', 2),
        	'category_id' => Input::get('category_id', 0),
        	'value_id'    => Input::get('value_id', 0),
        	'lon'         => Input::get('lon', 0),
        	'lat'         => Input::get('lat', 0)
        );


		//$post_id = Model_V1_Gochi::get_rank(0, 0, $option['call']);

		//$num = count($post_id);
		// for ($i=0; $i < $num; $i++) {
		// 	$tmp[$i] = Model_V1_Post::get_data(
		// 		$user_id, $sort_key, $post_id[$i]['post_id'], $option);
		// 	$data[$i] =  $tmp[$i][0];
		// }

        $data = Model_V1_Post::get_data($user_id, $sort_key, 0, $option);
	   	self::output_json($data);
	}


	//Comment Page
    public function action_comment()
    {
    	$sort_key = 'post';
    	$user_id  = session::get('user_id');
        $post_id  = Input::get('post_id');

		$post_data    = Model_V1_Post::get_data($user_id, $sort_key, $post_id);
	   	$comment_data = Model_V1_Comment::get_data($post_id);

	   	$data = array(
	   		"post" 		=> $post_data[0],
	   		"comments" 	=> $comment_data
	   	);

	   	self::output_json($data);
	}

	//Restaurant Page
	public function action_rest()
    {
    	$sort_key = 'rest';
    	$user_id  = session::get('user_id');
    	$rest_id  = Input::get('rest_id');

		$rest_data = Model_V1_Restaurant::get_data($user_id, $rest_id);
		$post_data = Model_V1_Post::get_data($user_id, $sort_key, $rest_id);

	   	$data = array(
	   		"restaurants" => $rest_data,
	   		"posts" => $post_data
	   	);

	   	self::output_json($data);
	}


	//User Page
	public function action_user()
	{
		$sort_key       = 'user';
		$user_id 		= session::get('user_id');
		$target_user_id = Input::get('target_user_id');

		$user_data = Model_V1_User::get_data($user_id, $target_user_id);
        $post_data = Model_V1_Post::get_data($user_id, $sort_key, $target_user_id, 0, 99);

	   	$data = array(
	   		"header" => $user_data,
	   		"posts"  => $post_data
	   	);

	   	self::output_json($data);
	}


	//Notice Page
	public function action_notice()
    {
    	$user_id = session::get('user_id');

    	$data = Model_V1_Notice::get_data($user_id);

	   	Model_V1_User::reset_badge($user_id);
	   	self::output_json($data);
	}


	//Near
	public function action_near()
	{
		$lon = Input::get('lon');
		$lat = Input::get('lat');

		$data = Model_V1_Restaurant::get_near($lon, $lat);

	   	self::output_json($data);
	}


	public function action_heatmap()
	{
		$data = Model_V1_Post::get_position();
		self::output_json($data);
	}


	//Follow
	public function action_follow()
	{
		$user_id = session::get('user_id');
		$target_user_id = Input::get('target_user_id');

		$data = Model_V1_Follow::get_follow($user_id, $target_user_id);

	   	self::output_json($data);
	}


	//Follower List
	public function action_follower()
	{
		$user_id = session::get('user_id');
		$target_user_id = Input::get('target_user_id');

		$data = Model_V1_Follow::get_follower($user_id, $target_user_id);

	   	self::output_json($data);
	}


	//行きたい登録リスト
	public function action_want()
	{
		$target_user_id = Input::get('target_user_id');

		$data = Model_V1_Want::get_want($target_user_id);

	   	self::output_json($data);
	}


	//応援店舗リスト
	public function action_user_cheer()
	{
		$target_user_id = Input::get('target_user_id');

		$data = Model_V1_Post::get_user_cheer($target_user_id);

	   	self::output_json($data);
	}


	//応援ユーザーリスト
	public function action_rest_cheer()
	{
		$user_id = Input::get('user_id');
		$rest_id = Input::get('rest_id');

		$data = Model_V1_Post::get_rest_cheer($rest_id);

		$num = count($data);

		for ($i=0; $i < $num; $i++) {
			$target_user_id = $data[$i]['user_id'];
			$follow_flag = Model_V1_Follow::get_flag($user_id, $target_user_id);
			$data[$i]['follow_flag'] = $follow_flag;
		}

	   	self::output_json($data);
	}


	public function action_user_search()
	{
		$user_id = session::get('user_id');
		$target_user_name = Input::get('username');

		$target_user_id = Model_V1_User::get_id($target_user_name);

		if (!empty($target_user_id)) {
			$data = array(
				'code' 	  => 200,
				'message' => 'ユーザーを見つけました',
				'user_id' => "$target_user_id"
		);

		}else{
			$data = array(
				'code' 	  => 500,
				'message' => 'ユーザーが見つかりませんでした',
				'user_id' => '0'
			);
		}

	   	self::output_json($data);
	}
}

