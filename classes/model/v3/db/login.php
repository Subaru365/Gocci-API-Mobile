<?php
/**
 * DB Login Model Class. 
 *
 * @package    Gocci-Mobile
 * @version    3.0.0 (2016/1/14)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @copyright  (C) 2016 Akira Murata
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

class Model_V3_Db_Login extends Model_V3_Db
{
	use SingletonTrait;

	/**
	 * @var String $table_name
	 */
	private static $table_name = 'logins';

	public function setLogin()
	{
		$this->insertData();
		$result = $this->query->execute();
	}

	private function insertData()
	{
		$this->query = DB::insert('logins')
		->set(array('login_user_id' => session::get('user_id')));
	}
}