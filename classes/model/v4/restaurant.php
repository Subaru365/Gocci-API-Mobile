<?php
/**
 * Restaurant Model Class.
 *
 * @package    Gocci-Mobile
 * @version    4.0.0 (2016/1/14)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @copyright  (C) 2016 Akira Murata
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

class Model_V4_Restaurant extends Model
{
	use SingletonTrait;

	/**
	 * @var Instance $rest
	 */
	private $rest;

	// Trait Override
	private function __construct()
	{
		$this->rest = Model_V4_Db_Restaurant::getInstance();
	}

	public function getRestData($rest_id)
	{
		$rest_data = $this->rest->getRestData($rest_id);
		$rest_data = Model_V4_Transcode::decodeLonLat($rest_data);

		return $rest_data;
	}
}