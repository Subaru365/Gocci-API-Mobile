<?php
/**
 * DB-Follow class.
 *
 * @package    Gocci-Mobile
 * @version    4.1.1 (2016/1/26)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @copyright  (C) 2016 Akira Murata
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

/**
 * @return Array
 */
class Model_V4_Db_Follow extends Model_V4_Db
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
			$flag = false;
		}else{
			$flag = true;
		}

		return $flag;
	}

	public function getFollowerRank($page)
	{
		$this->selectFollowerRankData();
		if ($page != 0) {
			$num = $page * 10;
			$this->query->offset($num);
		}
		$result = $this->run();
		return $result;
	}

	public function getFollowData($user_id)
	{
		$this->selectActiveData($user_id);
		$result = $this->run();
		return $result;
	}

	public function getFollowerData($user_id)
	{
		$this->selectPassiveData($user_id);
		$result = $this->run();
		return $result;
	}

	public function setFollow($user_id)
	{
		$this->insertData($user_id);
		$result = $this->query->execute();
		return $result[0];
	}


	public function setUnFollow($user_id)
	{
		$this->deleteData($user_id);
		$result = $this->query->execute();
		return $result;
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
		->from     (self::$table_name)
		->where    ('follow_a_user_id', session::get('user_id'))
		->and_where('follow_p_user_id', $user_id);
	}

	private function selectFollowerRankData()
	{
		$this->query = DB::select('follow_p_user_id')
		->from    (self::$table_name)
		->group_by('follow_p_user_id')
		->order_by(DB::expr('COUNT(*)'), 'desc')
		->limit   (10);
	}

	private function selectPassiveData($user_id)
	{
		$this->query = DB::select('user_id', 'username', 'profile_img')
		->from (self::$table_name)
		->join ('users', 'INNER')
		->on   ('follow_a_user_id', '=', 'user_id')
		->where('follow_p_user_id', $user_id);
	}

	private function selectActiveData($user_id)
	{
		$this->query = DB::select('user_id', 'username', 'profile_img')
		->from (self::$table_name)
		->join ('users', 'INNER')
		->on   ('follow_p_user_id', '=', 'user_id')
		->where('follow_a_user_id', $user_id);
	}

	private function insertData($user_id)
	{
		$this->query = DB::insert(self::$table_name)
		->set(array(
			'follow_a_user_id' => session::get('user_id'),
			'follow_p_user_id' => $user_id
		));
	}

	private function deleteData($user_id)
	{
		$this->query = DB::delete(self::$table_name)
		->where     ('follow_a_user_id', session::get('user_id'))
		->and_where ('follow_p_user_id', $user_id);
	}

}