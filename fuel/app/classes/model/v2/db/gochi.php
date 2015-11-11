<?php

class Model_Gochi extends Model
{
	public static function get_num($post_id)
	{
		$query = DB::select('gochi_id')
		->from ('gochis')
		->where('gochi_post_id', "$post_id");

		$result    = $query->execute()->as_array();
	   	$gochi_num = count($result);

		return $gochi_num;
	}


	public static function get_flag($post_id)
	{
		$query = DB::select('gochi_id')
		->from     ('gochis')
		->where    ('gochi_user_id', session::get('user_id'))
		->and_where('gochi_post_id', "$post_id");

		$result = $query->execute()->as_array();


		if ($result == true) {
			$gochi_flag = 1;
		}else{
			$gochi_flag = 0;
		}

		return $gochi_flag;
	}


	public static function get_user($post_id)
	{
		$query = DB::select('post_user_id')
		->from ('posts')
		->where('post_id', "$post_id");

		$result = $query->execute()->as_array();
		return $result;
	}


	public static function put_data($post_id)
	{
		$query = DB::insert('gochis')
		->set(array(
			'gochi_user_id' => session::get('user_id'),
			'gochi_post_id' => "$post_id"
		))
		->execute();
		return $query;
	}
}