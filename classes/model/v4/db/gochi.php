<?php
/**
 * DB-Gochi Model Class.
 *
 * @package    Gocci-Mobile
 * @version    4.0.0 (2016/1/14)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @copyright  (C) 2016 Akira Murata
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

/**
 * @return Array
 */
class Model_V4_Db_Gochi extends Model_V4_Db
{
	use SingletonTrait;

	/**
	 * @var String $table_name
	 */
	private static $table_name = 'gochis';


	public function getNum($post_id)
	{
		$this->selectIdForPost($post_id);
		$result = $this->run();
		$num = count($result);
		return $num;
	}

	public function getNumForUser($user_id)
	{
		$this->selectIdForUser($user_id);
		$result = $this->run();
		$num = count($result);
		return $num;
	}

	public function getPostId()
	{
		$this->selectPostId();
		$result = $this->run();
		return $result;
	}

	public function getFlag($post_id)
	{
		$this->selectIdForPost($post_id);
		$this->query->and_where('gochi_user_id', session::get('user_id'));
		$result = $this->run();

		if (empty($result)) {
			$flag = false;
		}else{
			$flag = true;
		}

		return $flag;
	}

	public function setGochi($post_id)
	{
		$this->insertData($post_id);
		$result = $this->query->execute();
		return $result[0];
	}

	public function setUnGochi($post_id)
	{
		$this->deleteData($post_id);
		$result = $this->query->execute();
		return $result[0];
	}

	//-----------------------------------------------------//

	private function selectIdForPost($post_id)
	{
		$this->query = DB::select('gochi_id')
		->from (self::$table_name)
		->where('gochi_post_id', $post_id);
	}

	private function selectIdForUser($user_id)
	{
		$this->query = DB::select('gochi_id')
		->from (self::$table_name)
		->join ('posts')
		->on   ('gochi_post_id', '=', 'post_id')
		->where('post_user_id', $user_id);
	}

	private function selectPostId()
	{
		$this->query = DB::select('gochi_post_id')
		->from (self::$table_name)
		->where('gochi_user_id', session::get('user_id'));
	}

	private function insertData($post_id)
	{
		$this->query = DB::insert(self::$table_name)
		->set(array(
			'gochi_user_id' => session::get('user_id'),
			'gochi_post_id' => $post_id
		));
	}

	private function deleteData($post_id)
	{
		$this->query = DB::delete(self::$table_name)
		->where('gochi_user_id', session::get('user_id'))
		->and_where('gochi_post_id', $post_id);
	}
}