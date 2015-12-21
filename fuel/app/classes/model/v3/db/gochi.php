<?php
/**
 * DB-Gochi Class.
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
class Model_V3_Db_Gochi extends Model_V3_Db
{
	use SingletonTrait;

	/**
	 * @var String $table_name
	 */
	private static $table_name = 'gochis';


	public function getNum($post_id)
	{
		$this->selectId($post_id);
		$result = $this->run();
		$num = count($result);
		return $num;
	}

	public function getFlag($post_id)
	{
		$this->selectId($post_id);
		$this->query->and_where('gochi_user_id', session::get('user_id'));
		$result = $this->run();
		
		if ($result == true) {
			$flag = 1;
		}else{
			$flag = 0;
		}

		return $flag;
	}

	public function setGochi($post_id)
	{
		$this->insertData($post_id);
		$result = $this->query->execute();
		return $result[0];
	}

	//-----------------------------------------------------//

	private function selectId($post_id)
	{
		$this->query = DB::select('gochi_id')
		->from (self::$table_name)
		->where('gochi_post_id', "$post_id");
	}


	private function insertData($post_id)
	{
		$this->query = DB::insert(self::$table_name)
		->set(array(
			'gochi_user_id' => session::get('user_id'),
			'gochi_post_id' => $post_id
		));
	}
}