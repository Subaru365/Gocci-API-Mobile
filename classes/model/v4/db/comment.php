<?php
/**
 * DB-Comment Class.
 *
 * @package    Gocci-Mobile
 * @version    4.0.0 (2016/1/14)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @copyright  (C) 2016 Akira Murata
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

/**
 * @return
 */
class Model_V4_Db_Comment extends Model_V4_Db
{
	use SingletonTrait;

	/**
	 * @var String $table_name
	 */
	private static $table_name = 'comments';

	public function getNum($post_id)
	{
		$this->selectId($post_id);
		$result = $this->run();

		$num = count($result);
		return $num;
	}

	public function getComment($post_id)
	{
		$this->selectData($post_id);
		$result = $this->run();
		return $result;
	}

	public function setComment($params)
	{
		$this->insertData($params);
		$result = $this->query->execute();
		return $result[0];
	}

	//-----------------------------------------------------//

	private function selectId($post_id)
	{
		$this->query = DB::select('comment_id')
		->from(self::$table_name)
		->where('comment_post_id', "$post_id");
	}


	private function selectData($post_id)
	{
		$this->query = DB::select(
			'comment_id', 'comment_user_id', 'username',
			'profile_img', 'comment', 'comment_date')
		->from(self::$table_name)

		->join('users', 'INNER')
		->on('comment_user_id', '=', 'user_id')

		->where('comment_post_id', "$post_id");
	}


	private function insertData($params)
	{
		$this->query = DB::insert(self::$table_name)
		->set(array(
			'comment_user_id' => session::get('user_id'),
			'comment_post_id' => $params['post_id'],
			'comment' 	      => $params['comment'],
		));
	}
}