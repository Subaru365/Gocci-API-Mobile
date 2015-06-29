<?php
class Model_Notice extends Model
{
     public static function get_data($user_id)
     {
     	$query = DB::select(
			'notices.notice_id', 'notices.notice_a_user_id', 'users.username',
			'users.profile_img', 'notices.notice', 'notices.notice_post_id',
			'notices.read_flag', 'notices.notice_date')

     	->from('notices')

		->join('users', 'INNER')
		->on('notices.notice_a_user_id', '=', 'users.user_id')

		->order_by('notices.notice_date','desc')

		->limit('15')

		->where('notices.notice_p_user_id', "$user_id");

		$notice_data = $query->execute()->as_array();


		$num = count($notice_data);

		for ($i=0; $i < $num; $i++) {

			//日付情報を現在との差分に書き換え

			$notice_date  = $notice_data[$i]['notice_date'];

			$date_diff = Model_Date::get_data($notice_date);
			$notice_data[$i]['notice_date'] = $date_diff;
		}

		return $notice_data;
     }


     //Notice登録
     public static function post_data(
     	$keyword, $a_user_id, $p_user_id, $post_id = 0)
     {
     	if ($keyword == 'gochi!') {
     		$category = 'like';

     	}elseif ($keyword == 'コメント') {
     		$category = 'comment';

     	}else{
     		$category = 'announce';
     	}


     	$query = DB::insert('notices')
     	->set(array(
     		'notice_a_user_id' => "$user_id",
     		'notice_p_user_id' => "$p_user_id",
     		'notice'           => "$notice",
     		'notice_post_id '  => "$post_id"
     	))
     	->execute();
     }
}
