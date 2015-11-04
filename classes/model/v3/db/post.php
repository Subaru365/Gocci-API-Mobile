<?php
/**
 * Authentication Class. Request SignUp, LogIn.
 *
 * @package    Gocci-Mobile
 * @version    3.0 (2015/11/02)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @license    MIT License
 * @copyright  2015 Inase,inc.
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

/**
 * @return Array
 */
class Model_V3_Post extends Model_Db
{
	/**
	 * @var String $table_name
	 */
	private static $table_name = 'posts';


	public function get_data()
	{

	}






	//"POST"取得
	private function get_data()
	{
		$this->query = DB::select(
			'post_id',		'movie',		'thumbnail',
			'category',		'tag',			'value',
			'memo', 		'post_date', 	'cheer_flag',
			'user_id', 		'username', 	'profile_img', 
			'rest_id', 		'restname',
			DB::expr('X(lon_lat) as lon, Y(lon_lat) as lat'),
			DB::expr("GLength(GeomFromText(CONCAT('LineString(${option['lon']} ${option['lat']},', X(lon_lat),' ', Y(lon_lat),')'))) as distance")
		)
		->from(self::$table_name)

		->join('restaurants', 'INNER')
		->on('post_rest_id', '=', 'rest_id')

		->join('users', 'INNER')
		->on('post_user_id', '=', 'user_id')

		->join('categories', 'LEFT OUTER')
		->on('post_category_id', '=', 'category_id')

		->join('tags', 'LEFT OUTER')
		->on('post_tag_id', '=', 'tag_id')

		->where('post_status_flag', '1')

		->limit(20);

		return $this->query;
	}


	private function get_sort($this->query, $option)
	{
		//並び替え
		if ($option['order_id'] == 0) {
		//時系列
			$this->query->order_by('post_date','desc');


		} elseif ($option['order_id'] == 1) {
		//近い順
			$this->query->order_by(DB::expr("GLength(GeomFromText(CONCAT('LineString(${option['lon']} ${option['lat']},', X(lon_lat),' ', Y(lon_lat),')')))"));


		} elseif ($option['order_id'] == 2) {
		//Gochi!ランキング

			//対象となる投稿の期間($interval)
			$interval = date("Y-m-d",strtotime("-1 month"));
			$now_date = date("Y-m-d",strtotime("+1 day"));

			$this->query->join('gochis', 'RIGHT')
			->on('gochi_post_id', '=', 'post_id')

			->where	   ('gochi_date', 'BETWEEN', array("$interval", "$now_date"))

			->group_by('gochi_post_id')
			->order_by(DB::expr('COUNT(gochi_post_id)'), 'desc');
		}


		//カテゴリー絞り込み
		if ($option['category_id'] != 0) {
			$this->query->where('category_id', $option['category_id']);
		}


		//価格絞り込み
		if ($option['value_id'] != 0) {
			if ($option['value_id'] == 1) {
				$this->query->where('value', 'between', array(1, 700));
			}
			if ($option['value_id'] == 2) {
				$this->query->where('value', 'between', array(500, 1500));
			}
			if ($option['value_id'] == 3) {
				$this->query->where('value', 'between', array(1500, 5000));
			}
			if ($option['value_id'] == 4) {
				$this->query->where('value', '>', 3000);
			}
		}


		//次ページ読み込み
		if ($option['call'] != 0) {
			$call_num = $option['call'] * $limit;
			$this->query->offset($call_num);
		}


		$this->query ->order_by('post_date','desc');
		$post_data = $this->query->execute()->as_array();

		return $post_data;
	}


	private function get_user($post_id)
	{
		$this->query = DB::select('post_user_id')->from(self::$table_name)
		->where('post_id', "$post_id");

		$post_user_id = $this->query->execute()->as_array();
		return $post_user_id[0]['post_user_id'];
	}


	//1ユーザーが応援している店舗リスト
	private function get_user_cheer($user_id)
	{
		$this->query = DB::select('rest_id', 'restname', 'locality')
		->from(self::$table_name)

		->join('restaurants', 'INNER')
		->on('post_rest_id', '=', 'rest_id')

		->where('post_user_id', "$user_id")
		->and_where('cheer_flag', '1')
		->and_where('post_status_flag', '1')

		->distinct(true);

		$cheer_list = $this->query->execute()->as_array();
		return $cheer_list;
	}


	//ユーザーに対する応援店数取得
	private function get_user_cheer_num($user_id)
	{
		$this->query = DB::select('post_id')->from(self::$table_name)

		->where	   ('post_user_id', "$user_id")
		->and_where('cheer_flag', '1')
		->and_where('post_status_flag', '1')

		->distinct(true);

		$result = $this->query->execute()->as_array();

		$cheer_num = count($result);
		return $cheer_num;
	}


	//1店舗に対して応援しているユーザーリスト
	private function get_rest_cheer($rest_id)
	{
		$this->query = DB::select('user_id', 'username', 'profile_img')
		->from(self::$table_name)

		->join('users', 'INNER')
		->on('post_user_id', '=', 'user_id')

		->where('post_rest_id', "$rest_id")
		->and_where('cheer_flag', '1')
		->and_where('post_status_flag', '1')

		->distinct(true);

		$cheer_list = $this->query->execute()->as_array();

		$num = count($cheer_list);

		for ($i=0; $i < $num; $i++) {
			$cheer_list[$i]['profile_img'] =　Model_Transcode::decode_profile_img($cheer_list[$i]['profile_img']);
		}

		return $cheer_list;
	}


	//店舗に対する応援総数
	private function get_rest_cheer_num($rest_id)
	{
		$this->query = DB::select('post_id')->from(self::$table_name)

		->where	   ('post_rest_id', "$rest_id")
		->and_where('cheer_flag', '1')
		->and_where('post_status_flag', '1');

		$result = $this->query->execute()->as_array();

		$cheer_num = count($result);
		return $cheer_num;
	}


	//動画投稿
	private function put_data($post_data)
	{
		$this->query = DB::insert('posts')
		->set(array(
			'post_user_id'      => session::get('user_id'),
			'post_rest_id'      => "$post_data['rest_id']",
			'movie'		        => "$post_data['movie']",
			'thumbnail'         => "$post_data['thumbnail']",
			'post_category_id'  => "$post_data['category_id']",
			'post_tag_id'	    => "$post_data['tag_id']",
			'value'        		=> "$post_data['value']",
			'memo'         		=> "$post_data['memo']",
			'cheer_flag'   		=> "$post_data['cheer_flag']"
		))
		->execute();
		
		return $this->query;
	}


	//投稿を表示
	private function post_publish($movie)
	{
		$this->query = DB::update('posts')
		->set  (array('post_status_flag' => '1'))
		->where('movie', "$movie");

		$result = $this->query->execute();
		return $result;
	}


	//投稿を消去
	private function delete_post($post_id)
	{
		$this->query = DB::update('posts')
		->set  (array('post_status_flag' => '0'))
		->where('post_id', "$post_id");

		$result = $this->query->execute();
		return $result;
	}


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

}
