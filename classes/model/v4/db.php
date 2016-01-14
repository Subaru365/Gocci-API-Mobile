<?php
/**
 * Authentication Class. Request SignUp, LogIn.
 *
 * @package    Gocci-Mobile
 * @version    4.0.0 (2016/1/14)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @copyright  (C) 2016 Akira Murata
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

class Model_V4_Db extends Model
{
	/**
	 * @var Instance $query
	 */
	protected $query;


	protected function run()
	{
		//$query  = $this->query;
		$result = $this->query->execute()->as_array();
		unset($this->query);
		return $result;
	}
}