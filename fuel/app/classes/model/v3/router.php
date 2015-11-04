<?php
/**
* Controllerからの処理をここに集合させ、ハブとなるクラスです
* Aws, DBの処理は必ずこのモデルを通過します
*/
class Model_V3_Router extends Model
{

	//=================================================================//
	//Post

	public static function get_timeline($option)
	{
		$query 		= Model_V2_Db_Post::get_data();

		$post_data  = Model_V2_Db_Post::get_sort($query, $option);

		$post_data  = self::option_post_data($post_data);
		return $post_data;
	}


	public static function get_followline($option)
	{
		$query 		= Model_V2_Db_Post::get_data();

		$follow_user_id = Model_Follow::get_follow_id();

		if (empty($follow_user_id)) {
			Controller_V1_Mobile_Base::output_error(301);
			exit;
		}

		$query->where('user_id', 'in', $follow_user_id);

		$post_data  = Model_V2_Db_Post::get_sort($query, $option);
		$post_data  = self::option_post_data($post_data);

		return $post_data;
	}


	public static function get_comment_post($post_id)
	{
		$query 		= Model_V2_Db_Post::get_data();
		$query      ->where('post_id', $post_id);

		$post_data  = $query->execute()->as_array();
		$post_data  = self::option_post_data($post_data);

		return $post_data[0];
	}


	public static function get_rest_post($rest_id)
	{
		$query 		= Model_V2_Db_Post::get_data();
		$query      ->where('rest_id', $rest_id);

		$post_data  = $query->execute()->as_array();
		$post_data  = self::option_post_data($post_data);

		return $post_data;
	}


	public static function get_user_post($option)
	{
		$query 		= Model_V2_Db_Post::get_data();
		$query      ->where('user_id', $sort_id);

		$post_data  = $query->execute()->as_array();
		$post_data  = self::option_post_data($post_data);

		return $post_data;
	}

	//-----------------------------------------------------------------//

	private static function option_post_data($post_data)
	{
		$post_num  = count($post_data);

		for ($i=0; $i < $post_num; $i++) {

			$post_data[$i]['mp4_movie']		= Model_V2_Transcode::decode_mp4_movie($post_data[$i]['movie']);
			$post_data[$i]['hls_movie']     = Model_V2_Transcode::decode_hls_movie($post_data[$i]['movie']);
			$post_data[$i]['thumbnail']     = Model_V2_Transcode::decode_thumbnail($post_data[$i]['thumbnail']);
			$post_data[$i]['profile_img']   = Model_V2_Transcode::decode_profile_img($post_data[$i]['profile_img']);
			$post_data[$i]['post_date']     = Model_V2_Transcode::decode_date($post_data[$i]['post_date']);

			//付加情報格納(like_num, comment_num, want_flag, follow_flag, like_flag)
			$post_data[$i]['gochi_num']		= Model_V2_Db_Gochi::get_num($post_data[$i]['post_id']);
			$post_data[$i]['comment_num']   = Model_V2_Db_Comment::get_num($post_data[$i]['post_id']);
			$post_data[$i]['want_flag']	    = Model_V2_Db_Want::get_flag($post_data[$i]['rest_id']);
			$post_data[$i]['follow_flag']   = Model_V2_Db_Follow::get_flag($post_data[$i]['user_id']);
			$post_data[$i]['gochi_flag']    = Model_V2_Db_Gochi::get_flag($post_data[$i]['post_id']);
		}

		return $post_data;
	}


	//=================================================================//
	//Other

	public static function get_comment($post_id)
	{
		$memo_data		= Model_V2_Db_Post::get_memo($post_id);
		$comment_data   = Model_V2_Db_Comment::get_data($post_id);

		//投稿者のmemoを$comment_dataに格納
		array_unsht($comment_data, $post_memo);

		$comment_num 	= count($comment_data);

		for ($i=1; $i < $comment_num; $i++) {
			$comment_data[$i]['re_user']	= Model_Re::get_data($comment_data[$i]['comment_id']);
		}

		return $comment_data;
	}


	public static function get_rest($rest_id)
	{
		$rest_data = Model_V2_Db_Restaurant::get_data($rest_id);

		$rest_data['want_flag']		= Model_Want::get_flag($rest_id);
		$rest_data['cheer_num']     = Model_Post::get_rest_cheer_num($rest_id);

		return $rest_data;
	}


	public static function get_user($target_user_id)
	{
		$user_data	= Model_V2_Db_User::get_data($target_user_id);

        $user_data['follow_num']	= Model_Follow::follow_num($target_user_id);
        $user_data['follower_num']  = Model_Follow::follower_num($target_user_id);
        $user_data['cheer_num']     = Model_Post::get_user_cheer_num($target_user_id);
        $user_data['want_num']      = Model_Want::want_num($target_user_id);
        $user_data['follow_flag']   = Model_Follow::get_flag($target_user_id);

        return $user_data;
	}


	public static function get_notice()
	{
    	$notice_data	= Model_V2_Notice::get_data();

    	$num = count($notice_data);

        for ($i=0; $i < $num; $i++) {
          	$notice_data[$i]['notice_date'] = Model_V2_Transcode::decode_date($notice_data[$i]['notice_date']);
            $notice_data[$i]['profile_img'] = Model_V2_Transcode::decode_profile_img($notice_data[$i]['profile_img']);
        }

	   	Model_User::reset_badge();
	   	return $notice_data;
	}

	public static function get_follow($user_id)
	{
		$follow_data 	= Model_V2_Follow::get_follow($user_id);
		return $follow_data;
	}

	public static function get_follower($user_id)
	{
		$follower_data 	= Model_V2_Follow::get_follower($user_id);
		return $follower_data;
	}

	public static function get_want($user_id)
	{
		$want_data		= Model_V2_Want::get_want($user_id);
		return $want_data;
	}

	public static function get_cheer($user_id)
	{
		$cheer_data 	= Model_V2_Post::get_user_cheer($user_id);
		return $cheer_data;
	}

	public static function get_post_user()
	{
		$target_user_id = Model_Post::get_user($post_id);
	}

	public static function get_search($username)
	{
		$user_id 		 = Model_V2_User::check_username($username);
		return $user_id;
	}

	//=================================================================//
	//Set
	//=================================================================//

	public static function set_pass($pass)
	{
		$hash_pass = Model_Validation::encryption_pass($pass);
		Model_User::update_pass($hash_pass);
	}

	public static function set_profile_img($img_url)
	{
		$profile_img = Model_S3::input($profile_img);
		$profile_img = Model_User::update_profile_img($profile_img);
	}

	public static function set_sns()
	{
		$identity_id = Model_User::get_identity_id();
		Model_User::update_sns_flag($provider);
		Model_Cognito::post_sns($identity_id, $provider, $token);
	}

	public static function unset_sns()
	{
		$identity_id = Model_User::get_identity_id($user_id);
		Model_User::delete_sns_flag($provider);
		Model_Cognito::delete_sns($identity_id, $provider, $token);
	}

	public static function set_post($post_data)
	{
		$post_data 	= Model_V2_Transcode::encode($post_data);
		Model_V2_Db_Post::set_data($post_data);
	}

	public static function set_gochi()
	{
		$user_id 	= Model_Gochi::post_gochi($post_id);
	}

	public static function set_comment()
	{
		$comment_id	= Model_Comment::post_comment($post_id, $comment);
	}

	public static function set_follow()
	{
		$result 	= Model_Follow::post_follow($follow_user_id);
	}

	public static function set_notice()
	{
		$record 	= Model_Notice::post_data($notice_data);


		$ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,
            'http://localhost/v1/mobile/background/publish/'
            .'?keyword='   . "$keyword"
            .'&a_user_id=' . session::get('user_id')
            .'&p_user_id=' . "$user_id"
        );

        curl_exec($ch);
        curl_close($ch);
	}

	public static function set_rest()
	{
		$rest_id 	= Model_Restaurant::post_add($rest_name, $lat, $lon);
	}

	public static function set_username()
	{
		$username 	= Model_User::update_name($new_username);
	}

	// public static function set_profile_img()
	// {
	// 	$profile_img = Model_User::update_profile_img($new_profile_img);
	// }

	public static function set_re()
	{
		$re_user_id = explode(',', $re_user_id);
		$num = count($re_user_id);

		for ($i=0; $i < $num; $i++) {
			Model_Re::post_data($comment_id, $re_user_id[$i]);
			Model_Notice::post_data($keyword, $re_user_id[$i], $post_id);
		}
	}
}