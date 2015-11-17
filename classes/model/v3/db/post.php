<?php
/**
 * Authentication Class. Request SignUp, LogIn.
 *
 * @package    Gocci-Mobile
 * @version    3.0 (2015/11/06)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @license    MIT License
 * @copyright  2015 Inase,inc.
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

/**
 * @return Array
 */
class Model_V3_Db_Post extends Model_V3_Db
{
	use SingletonTrait;

	/**
	 * @var String $table_name
	 */
	private static $table_name = 'posts';

	public function getNearPost($lon, $lat)
	{
		$this->selectNearData($lon, $lat);
		$result = $this->run();
		return $result;
	}

	public function getTimePost()
	{
		$this->selectTimeData();
		$result = $this->run();
		return $result;
	}

	public function getFollowPost($user_id)
	{
		$this->selectTimeData();
		$this->query->where('post_user_id', 'in', $user_id);
		$result = $this->run();
		return $result;
	}

	public function getPositionPost()
	{
		$this->selectPosition();
		$result = $this->run();
		return $result[0];
	}

	public function getUserPost($user_id)
	{
		$this->selectUserData($user_id);
		$result = $this->run();
		return $result;
	}

	public function getRestPost($rest_id)
	{
		$this->selectRestData($rest_id);
		$result = $this->run();
		return $result;
	}

	public function getUserCheerNum($user_id)
	{
		$this->selectCheer($user_id);
		$result = $this->run();
		$num = count($result);
		return $num;
	}

	//-----------------------------------------------------//

	private function selectCheer($user_id)
	{
		$this->query = DB::select('post_id')
		->from(self::$table_name)
		->where('post_user_id', $user_id)
		->and_where('cheer_flag', '1');
	}

	private function selectNearData($lon, $lat)
	{
		$this->query = DB::select(
			'post_id',		'movie',		'thumbnail',
			'rest_id', 		'restname',		'user_id',
		 	'username',		'cheer_flag',   'post_date',
			DB::expr("GLength(GeomFromText(CONCAT('LineString(
				${lon} ${lat},', X(lon_lat),' ', Y(lon_lat),')'))) as distance"
			)
		)
		->from(self::$table_name)

		->join('restaurants', 'INNER')
		->on('post_rest_id', '=', 'rest_id')

		->join('users', 'INNER')
		->on('post_user_id', '=', 'user_id')

		->order_by(DB::expr("GLength(GeomFromText(CONCAT('LineString(
			${lon} ${lat},', X(lon_lat),' ', Y(lon_lat),')')))"))

		->where('post_status_flag', '1')

		->limit(20);
	}


	private function selectTimeData()
	{
		$this->query = DB::select(
			'post_id',		'movie',		'thumbnail',
			'rest_id', 		'restname',		'user_id',
		 	'username',		'cheer_flag',   'post_date'
		)
		->from(self::$table_name)

		->join('restaurants', 'INNER')
		->on('post_rest_id', '=', 'rest_id')

		->join('users', 'INNER')
		->on('post_user_id', '=', 'user_id')

		->order_by('post_date','desc')

		->where('post_status_flag', '1')

		->limit(20);
	}


	private function selectPosition()
	{
		$this->query = DB::select('post_rest_id', 'restname',
			DB::expr('X(lon_lat) as lon, Y(lon_lat) as lat')
		)
		->from(self::$table_name)
		->join('restaurants', 'INNER')
		->on('post_rest_id', '=', 'rest_id')
		->distinct(true);
	}


	private function selectUserData($user_id)
	{
		$this->query = DB::select(
			'post_id',		'movie',		'thumbnail',
			'category',		'value',		'memo',
			'post_date', 	'cheer_flag',	'rest_id',
			'restname',
			DB::expr('X(lon_lat) as lon, Y(lon_lat) as lat')
		)
		->from(self::$table_name)

		->join('restaurants', 'INNER')
		->on('post_rest_id', '=', 'rest_id')

		->join('categories', 'LEFT OUTER')
		->on('post_category_id', '=', 'category_id')

		->order_by('post_date','desc')

		->where('post_user_id', $user_id)
		->and_where('post_status_flag', '1')

		->limit(20);
	}


	private function selectRestData($rest_id)
	{
		$this->query = DB::select(
			'post_id',		'movie',		'thumbnail',
			'category',		'value',		'memo',
			'post_date', 	'cheer_flag',	'post_rest_id',
			'user_id',		'username'
		)
		->from(self::$table_name)

		->join('users', 'INNER')
		->on('post_user_id', '=', 'user_id')

		->join('categories', 'LEFT OUTER')
		->on('post_category_id', '=', 'category_id')

		->order_by('post_date','desc')

		->where('post_rest_id', $rest_id)
		->and_where('post_status_flag', '1')

		->limit(20);
	}


	// private function select_data()
	// {
	// 	$this->query = DB::select(
	// 		'post_id',		'movie',		'thumbnail',
	// 		'category',		'value',		'memo',
	// 		'post_date', 	'cheer_flag',	'user_id',
	// 		'username', 	'profile_img',	'rest_id',
	// 		'restname',
	// 		DB::expr('X(lon_lat) as lon, Y(lon_lat) as lat'),
	// 	)
	// 	->from(self::$table_name)

	// 	->join('restaurants', 'INNER')
	// 	->on('post_rest_id', '=', 'rest_id')

	// 	->join('users', 'INNER')
	// 	->on('post_user_id', '=', 'user_id')

	// 	->join('categories', 'LEFT OUTER')
	// 	->on('post_category_id', '=', 'category_id')

	// 	->where('post_status_flag', '1')

	// 	->limit(20);
	// }


	private function decodeData($data)
	{
		$post_num  = count($data);

		for ($i=0; $i < $post_num; $i++) {
			$data[$i]['mp4_movie']	= Model_V3_Transcode::decode_mp4_movie($data[$i]['movie']);
			$data[$i]['hls_movie']  = Model_V3_Transcode::decode_hls_movie($data[$i]['movie']);
			$data[$i]['thumbnail']  = Model_V3_Transcode::decode_thumbnail($data[$i]['thumbnail']);
			$data[$i]['post_date']  = Model_V3_Transcode::decode_date($data[$i]['post_date']);
		}
		return $data;
	}


	private function decodeDistance($data)
	{
		$post_num  = count($data);

		for ($i=0; $i < $post_num; $i++) {
			$dis  		= $data[$i]['distance'];
			$dis_meter	= $dis * 112120;
			$data[$i]['distance'] = round($dis_meter);
		}
		return $data;
	}
}

	// private function get_sort($query, $option)
	// {
	// 	} elseif ($option['order_id'] == 2) {
	// 	//Gochi!ランキング

	// 		//対象となる投稿の期間($interval)
	// 		$interval = date("Y-m-d",strtotime("-1 month"));
	// 		$now_date = date("Y-m-d",strtotime("+1 day"));

	// 		$this->query->join('gochis', 'RIGHT')
	// 		->on('gochi_post_id', '=', 'post_id')

	// 		->where	   ('gochi_date', 'BETWEEN', array("$interval", "$now_date"))

	// 		->group_by('gochi_post_id')
	// 		->order_by(DB::expr('COUNT(gochi_post_id)'), 'desc');
	// 	}


	// 	//カテゴリー絞り込み
	// 	if ($option['category_id'] != 0) {
	// 		$this->query->where('category_id', $option['category_id']);
	// 	}


	// 	//価格絞り込み
	// 	if ($option['value_id'] != 0) {
	// 		if ($option['value_id'] == 1) {
	// 			$this->query->where('value', 'between', array(1, 700));
	// 		}
	// 		if ($option['value_id'] == 2) {
	// 			$this->query->where('value', 'between', array(500, 1500));
	// 		}
	// 		if ($option['value_id'] == 3) {
	// 			$this->query->where('value', 'between', array(1500, 5000));
	// 		}
	// 		if ($option['value_id'] == 4) {
	// 			$this->query->where('value', '>', 3000);
	// 		}
	// 	}


	// 	//次ページ読み込み
	// 	if ($option['call'] != 0) {
	// 		$call_num = $option['call'] * $limit;
	// 		$this->query->offset($call_num);
	// 	}


	// 	$this->query ->order_by('post_date','desc');
	// 	$post_data = $this->query->execute()->as_array();

	// 	return $post_data;
	// }


	// private function get_user($post_id)
	// {
	// 	$this->query = DB::select('post_user_id')->from(self::$table_name)
	// 	->where('post_id', "$post_id");

	// 	$post_user_id = $this->query->execute()->as_array();
	// 	return $post_user_id[0]['post_user_id'];
	// }


	// //1ユーザーが応援している店舗リスト
	// private function get_user_cheer($user_id)
	// {
	// 	$this->query = DB::select('rest_id', 'restname', 'locality')
	// 	->from(self::$table_name)

	// 	->join('restaurants', 'INNER')
	// 	->on('post_rest_id', '=', 'rest_id')

	// 	->where('post_user_id', "$user_id")
	// 	->and_where('cheer_flag', '1')
	// 	->and_where('post_status_flag', '1')

	// 	->distinct(true);

	// 	$cheer_list = $this->query->execute()->as_array();
	// 	return $cheer_list;
	// }


	// //ユーザーに対する応援店数取得
	// private function get_user_cheer_num($user_id)
	// {
	// 	$this->query = DB::select('post_id')->from(self::$table_name)

	// 	->where	   ('post_user_id', "$user_id")
	// 	->and_where('cheer_flag', '1')
	// 	->and_where('post_status_flag', '1')

	// 	->distinct(true);

	// 	$result = $this->query->execute()->as_array();

	// 	$cheer_num = count($result);
	// 	return $cheer_num;
	// }


	// //1店舗に対して応援しているユーザーリスト
	// private function get_rest_cheer($rest_id)
	// {
	// 	$this->query = DB::select('user_id', 'username', 'profile_img')
	// 	->from(self::$table_name)

	// 	->join('users', 'INNER')
	// 	->on('post_user_id', '=', 'user_id')

	// 	->where('post_rest_id', "$rest_id")
	// 	->and_where('cheer_flag', '1')
	// 	->and_where('post_status_flag', '1')

	// 	->distinct(true);

	// 	$cheer_list = $this->query->execute()->as_array();

	// 	$num = count($cheer_list);

	// 	for ($i=0; $i < $num; $i++) {
	// 		$cheer_list[$i]['profile_img'] =　Model_Transcode::decode_profile_img($cheer_list[$i]['profile_img']);
	// 	}

	// 	return $cheer_list;
	// }


	// //店舗に対する応援総数
	// private function get_rest_cheer_num($rest_id)
	// {
	// 	$this->query = DB::select('post_id')->from(self::$table_name)

	// 	->where	   ('post_rest_id', "$rest_id")
	// 	->and_where('cheer_flag', '1')
	// 	->and_where('post_status_flag', '1');

	// 	$result = $this->query->execute()->as_array();

	// 	$cheer_num = count($result);
	// 	return $cheer_num;
	// }


	// //動画投稿
	// private function put_data($post_data)
	// {
	// 	$this->query = DB::insert('posts')
	// 	->set(array(
	// 		'post_user_id'      => session::get('user_id'),
	// 		'post_rest_id'      => "$post_data['rest_id']",
	// 		'movie'		        => "$post_data['movie']",
	// 		'thumbnail'         => "$post_data['thumbnail']",
	// 		'post_category_id'  => "$post_data['category_id']",
	// 		'post_tag_id'	    => "$post_data['tag_id']",
	// 		'value'        		=> "$post_data['value']",
	// 		'memo'         		=> "$post_data['memo']",
	// 		'cheer_flag'   		=> "$post_data['cheer_flag']"
	// 	))
	// 	->execute();

	// 	return $this->query;
	// }


	// //投稿を表示
	// private function post_publish($movie)
	// {
	// 	$this->query = DB::update('posts')
	// 	->set  (array('post_status_flag' => '1'))
	// 	->where('movie', "$movie");

	// 	$result = $this->query->execute();
	// 	return $result;
	// }


	// //投稿を消去
	// private function delete_post($post_id)
	// {
	// 	$this->query = DB::update('posts')
	// 	->set  (array('post_status_flag' => '0'))
	// 	->where('post_id', "$post_id");

	// 	$result = $this->query->execute();
	// 	return $result;
	// }


	// private function get_memo($post_id)
	// {
	// 	$this->query = DB::select('user_id', 'username', 'profile_img', 'memo', 'post_date')
	// 	->from(self::$table_name)

	// 	->join('users', 'INNER')
	// 	->on('post_user_id', '=', 'user_id')

	// 	->where('post_id', "$post_id");

	// 	$value = $this->query->execute()->as_array();

	// 	$re_user = array();
	// 	array_push ($value[0], $re_user);

	// 	$key = array('comment_user_id', 'username', 'profile_img', 'comment', 'comment_date', 're_user');
	// 	$post_comment = array_combine($key, $value[0]);

	// 	return $post_comment;
	// }