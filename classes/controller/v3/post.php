<?php
/**
* 
*/
class Controller_V3_Post extends Controller_V2_Mobile_Base
{
	public function before()
	{

	}
	
	public function put_index()
	{
		//$input is rest_id, movie_name, category_id, tag_id, value, memo, cheer_flag
		$result = Model_V2_Router::put_post($post_data);
		self::output_success($result);
	}

	public function delete_index($post_id)
	{
		$result = Model_Post::post_delete($post_id);
		self::success($keyword);
	}
	
	public function get_nearlime()
	{
		//$option is [call, order_id, category_id, value_id, lon, lat]
		$input_data  = Model_V2_Router::nearline($option);
		self::output_success($post_data);
	}

	public function get_timeline()
	{
		//$option is [call, order_id, category_id, value_id, lon, lat]
		$post_data  = Model_V2_Router::timeline($option);
	   	self::output_success($post_data);
	}

	public function get_followline()
	{
		//$option is [call, order_id, category_id, value_id, lon, lat]
		$post_data	= Model_V2_Router::followline($option);
	   	self::output_success($post_data);
	}

	// public function get_comment($post_id)
	// {
	// 	$post_data   	= Model_V2_Router::comment_post($post_id);
	//    	$comment_data   = Model_V2_Router::comment($post_id);

	//    	$data = array(
	//    		"post" 		=> $post_data,
	//    		"comments" 	=> $comment_data
	//    	);

	//    	self::output_success($data);
	// }

	public function put_comment($post_id)
	{
		//$input is comment
		$result = Model_V2_Router::put_comment($post_data);
		self::output_success($result);
	}

	public function put_gochi($post_id)
	{
		$result = Model_V2_Router::put_gochi($post_id);
		self::output_success($result);
	}
}