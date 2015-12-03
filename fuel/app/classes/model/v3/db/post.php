<?php
/**
 * DB-Post Model Class.
 *
 * @package    Gocci-Mobile
 * @version    3.0 (2015/11/26)
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

	/**
	 * @var Integer $limit
	 */
	private $limit;


	private function __construct($limit = 20)
	{
		$this->limit = $limit;
	}


	public function getNearPost($params)
	{
		$this->selectNearData($params['lon'], $params['lat']);
		$this->addOption($params);
		$result = $this->run();
		return $result;
	}

	public function getTimePost($params)
	{
		$this->selectTimeData();
		$this->addOption($params);
		$result = $this->run();
		return $result;
	}

	public function getFollowPost($params)
	{
		$this->selectTimeData();
		$this->query->where('post_user_id', 'in', $params['follow_user_id']);
		$this->addOption($params);
		$result = $this->run();
		return $result;
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

	public function getPositionPost()
	{
		$this->selectPosition();
		$result = $this->run();
		return $result;
	}

	public function getPostUserId($post_id)
	{
		$this->selectUserId($post_id);
		$result = $this->run();
		return $result[0]['post_user_id'];
	}

	public function getUserCheerNum($user_id)
	{
		$this->selectCheer($user_id);
		$result = $this->run();
		$num = count($result);
		return $num;
	}

	public function getMemo($post_id)
	{
		$this->selectMemo($post_id);
		$result = $this->run();
		return $result;
	}

	public function getUserCheer($user_id)
	{
		$this->selectUserCheer($user_id);
		$result = $this->run();
		return $result;
	}

	public function postDelete($post_id)
	{
		$this->updatePostHide($post_id);
		$result = $this->query->execute();
		return $result;
	}

	public function setHashId($post_id, $hash_id)
	{
		$this->updateHash($post_id, $hash_id);
		$result = $this->query->execute();
		return $result;
	}

	public function setUnPostData($post_id)
	{
		$this->updateUnPost($post_id);
		$result = $this->query->execute();
		return $result;
	}

	public function setPostData($params)
	{
		$this->insertData($params);
		$result = $this->query->execute();
		return $result[0];
	}

	//-----------------------------------------------------//

	private function selectUserId($post_id)
	{
		$this->query = DB::select('post_user_id')
		->from(self::$table_name)
		->where('post_id', $post_id);
	}

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
			'value', 		'rest_id', 		'restname',
			'user_id', 		'username',		'cheer_flag',
			'post_date',
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

		->limit($this->limit);
	}


	private function selectTimeData()
	{
		$this->query = DB::select(
			'post_id',		'movie',		'thumbnail',
			'value', 		'rest_id', 		'restname',
			'user_id', 		'username',		'cheer_flag',
			'post_date'
		)
		->from(self::$table_name)

		->join('restaurants', 'INNER')
		->on('post_rest_id', '=', 'rest_id')

		->join('users', 'INNER')
		->on('post_user_id', '=', 'user_id')

		->order_by('post_date','desc')

		->where('post_status_flag', '1')

		->limit($this->limit);
	}


	private function selectPosition()
	{
		$this->query = DB::select(
			'post_rest_id',	'restname',
			DB::expr('X(lon_lat) as lon, Y(lon_lat) as lat')
		)
		->from(self::$table_name)

		->join('restaurants', 'INNER')
		->on('post_rest_id', '=', 'rest_id')
		
		->distinct(true)
		->limit(100000);
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

		->limit($this->limit);
	}


	private function selectRestData($rest_id)
	{
		$this->query = DB::select(
			'post_id',		'movie',		'thumbnail',
			'category',		'value',		'memo',
			'post_date', 	'cheer_flag',	'post_rest_id',
			'user_id',		'username',     'profile_img'
		)
		->from(self::$table_name)

		->join('users', 'INNER')
		->on('post_user_id', '=', 'user_id')

		->join('categories', 'LEFT OUTER')
		->on('post_category_id', '=', 'category_id')

		->order_by('post_date','desc')

		->where('post_rest_id', $rest_id)
		->and_where('post_status_flag', '1')

		->limit($this->limit);
	}


	private function selectMemo($post_id)
	{
		$this->query = DB::select(
			'user_id',	'username',	'profile_img',
			'memo', 	'post_date'
		)
		->from(self::$table_name)

		->join('users', 'INNER')
		->on('post_user_id', '=', 'user_id')

		->where('post_id', "$post_id");
	}


	private function selectUserCheer($user_id)
	{
		$this->query = DB::select('rest_id', 'restname', 'locality')
		->from(self::$table_name)

		->join('restaurants', 'INNER')
		->on('post_rest_id', '=', 'rest_id')

		->where('post_user_id', $user_id)
		->and_where('cheer_flag', '1')
		->and_where('post_status_flag', '1')

		->distinct(true);
	}

	//-----------------------------------------------------//

	private function updateHash($post_id, $hash_id)
	{
		$this->query = DB::update(self::$table_name)
		->value('post_hash_id', $hash_id)
		->where('post_id', $post_id);
	}

	private function updatePostHide($post_id)
	{
		$this->query = DB::update(self::$table_name)
		->value('post_status_flag', 0)
		->where('post_id', $post_id);
	}


	//-----------------------------------------------------//

	private function insertData($data)
	{
		$this->query = DB::insert(self::$table_name)
		->set(array(
			'post_user_id'		=> session::get('user_id'),
			'post_rest_id'      => $data['rest_id'],
			'movie'		        => $data['movie'],
			'thumbnail'         => $data['thumbnail'],
			'post_category_id'  => $data['category_id'],
			'value'        		=> $data['value'],
			'memo'         		=> $data['memo'],
			'cheer_flag'   		=> $data['cheer_flag'],
		));
	}


	private function addOption($option)
	{
		//カテゴリー絞り込み
		if (!empty($option['category_id'])) {
			$this->query
			->where('category_id', $option['category_id'])
			->join('categories', 'LEFT OUTER')
			->on('post_category_id', '=', 'category_id');
		}

		//価格絞り込み
		if (!empty($option['value_id'])) {
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
		if (!empty($option['page'])) {
			$num = $option['page'] * $this->limit;
			$this->query->offset($num);
		}
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
