<?php
/**
 * DB-Want Model Class.
 *
 * @package    Gocci-Mobile
 * @version    3.0 (2015/11/17)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @license    MIT License
 * @copyright  2015 Inase,inc.
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

/**
 * @return Array
 */
class Model_V3_Db_Want extends Model_V3_Db
{
	use SingletonTrait;

	/**
	 * @var String $table_name
	 */
	private static $table_name = 'wants';


	public function getWantNum($user_id)
	{
		$this->selectId($user_id);
		$result = $this->run();
		$num = count($result);
		return $num;
	}

	public function getMyFlag($rest_id)
	{
		$this->selectId(session::get('user_id'));
		$this->query->and_where('want_rest_id', $rest_id);
		$result = $this->run();

		if ($result == true) {
			$flag = 1;
		}else{
			$flag = 0;
		}

		return $flag;
	}

	//-----------------------------------------------------//

	private function selectId($user_id)
	{
		$this->query = DB::select('want_id')
		->from(self::$table_name)
		->where('want_user_id', $user_id);
	}


	private function get_data($user_id)
	{
		$query = DB::select(
			'rest_id', 'restname', 'locality'
		)
		->from(self::$table_name)

		->join('restaurants', 'INNER')
		->on  ('want_rest_id', '=', 'rest_id')

		->where('want_user_id', "$user_id");

		$want_data = $query->execute()->as_array();
		return $want_data;
	}

	//行きたい登録
	private function put_want($want_rest_id)
	{
		$query = DB::insert(self::$table_name)
		->set(array(
			'want_user_id' => session::get('user_id'),
			'want_rest_id' => "$want_rest_id"
		));

		$result = $query->execute();
		return $result;
	}


	//行きたい解除
	private function delete_want($want_rest_id)
	{
		$query = DB::delete(self::$table_name)
		->where     ('want_user_id', session::get('user_id'))
		->and_where ('want_rest_id', "$want_rest_id");

		$result = $query->execute();
		return $result;
	}
}