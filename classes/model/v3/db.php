<?php
/**
 * Authentication Class. Request SignUp, LogIn.
 *
 * @package    Gocci-Mobile
 * @version    3.0 (2015/10/26)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @license    MIT License
 * @copyright  2015 Inase,inc.
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

class Model_V3_Db extends Model
{
	/**
	 * @var String $table_name
	 */
	protected static $table_name = '';

	/**
	 * @var Array $result
	 */
	protected $result = array();

	/**
	 * @var Instance $query
	 */
	protected $query;

	protected function run()
	{
		$result 		= $this->query->execute()->as_array();
		$this->result 	= $result;
	}
}