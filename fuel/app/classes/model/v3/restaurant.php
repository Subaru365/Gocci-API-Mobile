<?php
/**
 * Get  Class
 *
 * @package    Gocci-Mobile
 * @version    3.0 (2015/11/16)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @license    MIT License
 * @copyright  2015 Inase,inc.
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

class Model_V3_Restaurant extends Model
{
	use SingletonTrait;

	/**
	 * @var Instance $rest
	 */
	private $rest;

	// Trait Override
	private function __construct()
	{
		$this->rest = Model_V3_Db_Restaurant::getInstance();
	}

	public function getRestData($rest_id)
	{
		$want = Model_V3_Db_Want::getInstance();
		$post = Model_V3_Db_Post::getInstance();

		$rest_data = $this->rest->getRestData($rest_id);

		$rest_data['want_flag']		= $want->getMyFlag($rest_id);
		//$rest_data['cheer_num']     = $post->getRestCheerNum($rest_id);

		return $rest_data;
	}
}