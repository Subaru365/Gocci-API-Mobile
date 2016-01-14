<?php
/**
 * DB-Block Model Class.
 *
 * @package    Gocci-Mobile
 * @version    4.0.0 (2016/1/14)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @copyright  (C) 2016 Akira Murata
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

class Model_V4_Db_Block extends Model_V4_Db
{
	use SingletonTrait;

	/**
	 * @var String $table_name
	 */
	private static $table_name = 'blocks';


	public function setBlock($post_id)
	{
		$this->insertData($post_id);
		$result = $this->query->execute();
		return $result;
	}

	private function insertData($post_id)
	{
		$this->query = DB::insert(self::$table_name)
		->set(array(
			'block_user_id' => session::get('user_id'),
			'block_post_id' => $post_id,
		));
	}
}