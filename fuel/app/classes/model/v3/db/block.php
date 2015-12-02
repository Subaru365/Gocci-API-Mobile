<?php
/**
 * DB-Block Model Class.
 *
 * @package    Gocci-Mobile
 * @version    3.0 (2015/11/20)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @license    MIT License
 * @copyright  2015 Inase,inc.
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

class Model_V3_Db_Block extends Model_V3_Db
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