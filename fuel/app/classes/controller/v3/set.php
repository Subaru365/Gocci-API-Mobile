<?php
/*
* SET API
*　関数名の内容を保存します。
*/

class Controller_V3_Set extends Controller_V2_Mobile_Base
{
	public function action_password()
	{
		//Input is $pass
		Model_User::update_pass($hash_pass);
	}


	//SNS Link
	public function action_sns_link()
	{
		//$input is username, provider, token, profile_img

		if (!empty($profile_img))
		{

		}

		Model_V2_Router::set_sns();
	}


	public function action_sns_unlink()
	{
		//input is user_id, provider, token

		Model_V2_Router::unset_sns();
	}


	//Gochi!
	public function action_gochi()
	{
		//Input post_id

			if ($user_id != $target_user_id) {

			}

		Model_V2_Router::set_gochi();
	}


	//Comment
	public function action_comment()
	{
		//Input post_id, comment, re_user_id

		if ($user_id != $target_user_id) {

		}

		Model_V2_Router::set_comment();
	}


	//Follow
	public function action_follow()
	{
		//Input target_user_id

		try
		{
			$result = Model_Follow::post_follow($user_id, $follow_user_id);

			$record = Model_Notice::post_data(
				$keyword, $user_id, $follow_user_id);

		Model_V2_Router::set_follow();
	}


	//Unfollow
	public function action_unfollow()
	{
		//Input target_user_id

			$result = Model_Follow::post_unfollow($user_id, $unfollow_user_id);
			self::success($keyword);
	}


	//Want
	public function action_want()
	{
		//Input rest_id
			$result = Model_Want::post_want($user_id, $rest_id);
			self::success($keyword);
	}


	//UnWant
	public function action_unwant()
	{
		$rest_id = Input::get('rest_id');

		$result = Model_Want::post_unwant($user_id, $rest_id);
		self::success($keyword);

	}


	//Post
	public function action_post()
	{
		//Input rest_id, movie_name, category_id, tag_id, value, memo, cheer_flag

		$result = Model_Post::post_data(
			$user_id, $rest_id, $movie_name,
			$category_id, $tag_id, $value, $memo, $cheer_flag);
		self::success($keyword);
	}


	//PostBlock
	public function action_postblock()
	{
		//Input post_id
		$result = Model_Block::post_block($user_id, $post_id);
		self::success($keyword);

	}


	//PostDelete
	public function action_postdel()
	{
		//Input post_id

		$result = Model_Post::post_delete($post_id);
		self::success($keyword);
	}


	//プロフィール編集
	public function action_username()
	{
		//Input username

	}


	public function action_profile_img()
	{
		//Input profile_img
	}


	//Feedback
	public function action_feedback()
	{
		//Input feedback
		$result = Model_Feedback::post_add($user_id, $feedback);
		self::success($keyword);
	}


	//RestAdd
	public function action_rest()
	{
		//Input restname, lat, lon
		$rest_id = Model_Restaurant::post_add($rest_name, $lat, $lon);
	}
}