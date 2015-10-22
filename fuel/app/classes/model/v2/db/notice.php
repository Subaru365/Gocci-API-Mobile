<?php

class Model_Notice extends Model
{
    public static function get_data()
    {
        $query = DB::select(
    		    'notice_id',  'notice_a_user_id',  'username',  'profile_img',
            'notice',     'notice_post_id',    'read_flag', 'notice_date'
        )
        ->from('notices')

        ->join('users', 'INNER')
        ->on('notice_a_user_id', '=', 'user_id')

        ->join('posts', 'INNER')
        ->on('notice_post_id', '=', 'post_id')

        ->order_by('notice_date','desc')

        ->limit('15')
        ->where('notice_p_user_id', session::get('user_id'))
        ->where('post_status_flag', '1');

        $notice_data = $query->execute()->as_array();
    		return $notice_data;
    }


    //Notice登録
    public static function put_data($notice_data)
    {
       	// if ($keyword == 'gochi!') {
       	// 	  $notice = 'like';

       	// }elseif ($keyword == 'コメント') {
       	//   	$notice = 'comment';

        // }elseif ($keyword == 'フォロー') {
        //     $notice = 'follow';

        // }else{
       	//   	$notice = 'announce';
       	// }

       	$query = DB::insert('notices')
       	->set(array(
       		  'notice_a_user_id' => session::get('user_id'),
       		  'notice_p_user_id' => "$notice_data['user_id']",
       		  'notice'           => "$notice_data['notice']",
       		  'notice_post_id'   => "$notice_data['post_id']"
       	))
       	->execute();
        
        return $query;
    }
}
