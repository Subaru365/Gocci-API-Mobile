<?php
/**
 * Authentication Class. Request SignUp, LogIn.
 *
 * @package    Gocci-Mobile
 * @version    3.0 (2015/11/16)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @license    MIT License
 * @copyright  2015 Inase,inc.
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

/**
 * @return Array
 */
class Model_V3_Db_Follow extends Model_V3_Db
{
	use SingletonTrait;

	/**
	 * @param String $table_name
	 */
	private static $table_name = 'follows';


	public function getFollowId()
	{
		$this->selectFollowId();
		$result = $this->run();
		return $result;
	}


	//自分がフォローしてるかフラグで返す
	public static function get_flag($target_user_id)
	{
		$query = DB::select('follow_id')
		->from     ('follows')
		->where    ('follow_a_user_id', session::get('user_id'))
		->and_where('follow_p_user_id', "$target_user_id");

		$result = $query->execute()->as_array();


		if (empty($result)) {
			$follow_flag = 0;
		}else{
			$follow_flag = 1;
		}

		return $follow_flag;
	}


	public function selectFollowId()
	{
		$this->query = DB::select('follow_p_user_id')
		->from(self::$table_name)
		->where('follow_a_user_id', session::get('user_id'));
	}


	//followしているユーザー情報
	public static function get_follow($target_user_id)
	{
		$query = DB::select('user_id', 'username', 'profile_img')
		->from ('follows')
		->join ('users', 'INNER')
		->on   ('follow_p_user_id', '=', 'user_id')
		->where('follow_a_user_id', "$target_user_id");

		$result 		= $query->execute()->as_array();

		$follow_list    = self::add_flag($result);
		return $follow_list;
	}


	//フォローされてるユーザー情報
	public static function get_follower($target_user_id)
	{
		$query = DB::select('user_id', 'username', 'profile_img')
		->from ('follows')
		->join ('users', 'INNER')
		->on   ('follow_a_user_id', '=', 'user_id')
		->where('follow_p_user_id', "$target_user_id");

		$result			= $query->execute()->as_array();

		$follower_list  = self::add_flag($result);
		return $follower_list;
	}


	//フォロー数を返す
	public static function get_follow_num($user_id)
	{
		$query = DB::select('follow_id')
		->from ('follows')
		->where('follow_a_user_id', "$user_id");

		$result = $query->execute()->as_array();

		$follow_num = count($result);
		return $follow_num;
	}


	//フォロワー数を返す
	public static function get_follower_num($user_id)
	{
		$query = DB::select('follow_id')
		->from ('follows')
		->where('follow_p_user_id', "$user_id");

		$result = $query->execute()->as_array();

		$follower_num = count($result);
		return $follower_num;
	}


	//フォロー登録
	public static function put_data($user_id)
	{
		$query = DB::insert('follows')
		->set(array(
			'follow_a_user_id' => session::get('user_id'),
			'follow_p_user_id' => "$target_user_id"
		));

		$result = $query->execute();
		return $result;
	}


	//フォロー解除
	public static function delete_data($user_id)
	{
		$query = DB::delete('follows')
		->where     ('follow_a_user_id', session::get('user_id'))
		->and_where ('follow_p_user_id', "$target_user_id");

		$result = $query->execute();
		return $result;
	}


	//------------------------------------------------------------//
	protected static function add_flag($data)
	{
		$num = count($data);

		for ($i=0; $i < $num; $i++) {
			$data[$i]['profile_img'] = Model_Transcode::decode_profile_img($data[$i]['profile_img']);
			$data[$i]['follow_flag'] = self::get_flag($data[$i]['user_id']);
		}

		return $data;
	}
}