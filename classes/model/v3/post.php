<?php
/**
 * Authentication Class. Request SignUp, LogIn.
 *
 * @package    Gocci-Mobile
 * @version    3.0 (2015/11/06)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @license    MIT License
 * @copyright  2015 Inase,inc.
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

/**
 * @return Array
 */
class Model_V3_Post extends Model
{
	/**
	 * @var String $table_name
	 */
	private static $table_name = 'posts';

	/**
	 * @var Array $position
	 */
	private $position = array();

	private function __construct()
	{

	}

	public static function get_instance()
	{
		static $instance = null;

		if (null === $instance) {
			$instance = new static();
		}
		return $instance;
	}
}

