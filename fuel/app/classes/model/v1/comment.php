<?php
class Model_V1_Comment extends Model
{

	public static function get_data($post_id)
	{
		$query = DB::select(
			'comment_id', 'comment_user_id', 'username',
			'profile_img', 'comment', 'comment_date')
		->from('comments')

		->join('users', 'INNER')
		->on('comment_user_id', '=', 'user_id')

		->where('comment_post_id', "$post_id");

		$comment_data = $query->execute()->as_array();

		//投稿者のコメントを$comment_data[0]に格納
		$post_comment = Model_V1_Post::get_memo($post_id);
		array_unshift($comment_data, $post_comment);


		$comment_num  = count($comment_data);

		for ($i=0; $i < $comment_num; $i++) {

			$comment_data[$i]['profile_img'] =
				Model_V1_Transcode::decode_profile_img($comment_data[$i]['profile_img']);

			//日付情報を現在との差分に書き換え
			$comment_data[$i]['comment_date'] =
				Model_V1_Date::get_data($comment_data[$i]['comment_date']);
		}

		for ($i=1; $i < $comment_num; $i++) {
			$comment_data[$i]['re_user'] =
				Model_V1_Re::get_data($comment_data[$i]['comment_id']);
		}

		return $comment_data;
	}


	//コメント数取得
	public static function get_num($post_id)
	{
		$query = DB::select('comment_id')->from('comments')
		->where('comment_post_id', "$post_id");

		$result = $query->execute()->as_array();
	   	$comment_num = count($result);

		return $comment_num;
	}


	//コメント登録
	public static function post_comment($user_id, $post_id, $comment)
	{
		$query = DB::insert('comments')
		->set(array(
			'comment_user_id' => "$user_id",
			'comment_post_id' => "$post_id",
			'comment' 	      => "$comment"
		))
		->execute();

		return $query[0];
	}
}