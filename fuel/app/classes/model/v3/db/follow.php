<?php
/**
 * DB-Follow class.
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
		$this->selectFollowId(session::get('user_id'));
		$result = $this->run();
		return $result;
	}

	public function getFollowNum($user_id)
	{
		$this->selectFollowId($user_id);
		$result = $this->run();
		$num = count($result);
		return $num;
	}

	public function getFollowerNum($user_id)
	{
		$this->selectFollowerId($user_id);
		$result = $this->run();
		$num = count($result);
		return $num;
	}

	public function getFollowFlag($user_id)
	{
		$this->selectFollowFlag($user_id);
		$result = $this->run();

		if (empty($result)) {
			$flag = 0;
		}else{
			$flag = 1;
		}

		return $flag;
	}

	//-----------------------------------------------------//

	private function selectFollowId($user_id)
	{
		$this->query = DB::select('follow_p_user_id')
		->from(self::$table_name)
		->where('follow_a_user_id', $user_id);
	}

	private function selectFollowerId($user_id)
	{
		$this->query = DB::select('follow_id')
		->from (self::$table_name)
		->where('follow_p_user_id', $user_id);
	}

	private function selectFollowFlag($user_id)
	{
		$this->query = DB::select('follow_id')
		->from     ('follows')
		->where    ('follow_a_user_id', session::get('user_id'))
		->and_where('follow_p_user_id', $user_id);
	}


	//followしているユーザー情報
	private function get_follow($target_user_id)
	{
		$this->query = DB::select('user_id', 'username', 'profile_img')
		->from ('follows')
		->join ('users', 'INNER')
		->on   ('follow_p_user_id', '=', 'user_id')
		->where('follow_a_user_id', "$target_user_id");

		$result 		= $this->query->execute()->as_array();

		$follow_list    = self::add_flag($result);
		return $follow_list;
	}


	//フォローされてるユーザー情報
	private function get_follower($target_user_id)
	{
		$this->query = DB::select('user_id', 'username', 'profile_img')
		->from ('follows')
		->join ('users', 'INNER')
		->on   ('follow_a_user_id', '=', 'user_id')
		->where('follow_p_user_id', "$target_user_id");

		$result			= $this->query->execute()->as_array();

		$follower_list  = self::add_flag($result);
		return $follower_list;
	}





	//フォロワー数を返す
	private function get_follower_num($user_id)
	{
		$this->query = DB::select('follow_id')
		->from ('follows')
		->where('follow_p_user_id', "$user_id");

		$result = $this->query->execute()->as_array();

		$follower_num = count($result);
		return $follower_num;
	}


	//フォロー登録
	private function put_data($user_id)
	{
		$this->query = DB::insert('follows')
		->set(array(
			'follow_a_user_id' => session::get('user_id'),
			'follow_p_user_id' => "$target_user_id"
		));

		$result = $this->query->execute();
		return $result;
	}


	//フォロー解除
	private function delete_data($user_id)
	{
		$this->query = DB::delete('follows')
		->where     ('follow_a_user_id', session::get('user_id'))
		->and_where ('follow_p_user_id', "$target_user_id");

		$result = $this->query->execute();
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