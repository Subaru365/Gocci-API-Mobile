<?php
/**
 * DB-Re Model Class.
 *
 * @package    Gocci-Mobile
 * @version    3.0 (2015/11/17)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @license    MIT License
 * @copyright  2015 Inase,inc.
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

class Model_V3_Db_Re extends Model_V3_Db
{
	use SingletonTrait;

	/**
	 * @var Instance $table_name
	 */
	private static $table_name = 'res';


	public function getRe($comment_id)
	{
		$this->getData($comment_id);
		$result = $this->run();
		return $result;
	}

	public function setRe($comment_id, $re_user_id)
	{
		$re_user_id = explode(',', $re_user_id);
		$num = count($re_user_id);

		for ($i=0; $i < $num; $i++) { 
			$this->setData($comment_id, $re_user_id[$i]);
			$this->query->execute();
		}
	}


	//=====================================================//


	private function getData($comment_id)
	{
		$this->query = DB::select('user_id', 'username')
		->from(self::$table_name)

		->join('users', 'INNER')
		->on('re_user_id', '=', 'user_id')

		->where('re_comment_id', $comment_id);
	}


	private function setData($comment_id, $re_user_id)
	{
		$this->query = DB::insert(self::$table_name)
		->set(array(
			're_comment_id' => $comment_id,
			're_user_id' 	=> $re_user_id,
		));
	}
}