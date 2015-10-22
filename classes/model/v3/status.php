<?php
/**
 * Status Code and Message list.
 *
 * @package    Gocci-Mobile
 * @version    3.0 (2015/10/21)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @license    MIT License
 * @copyright  2015 Inase,inc.
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

/** @return Array $status */
class Model_V3_Status extends Model
{
	/** @var Array $result */
	private $result;


	private function before()
	{
		$this->result = array(
			'version'   => 3.0,
			'uri'       => Uri::string(),
			'code'      => 'SUCCESS',
			'message'   => 'Successful API request'
		);
	}

	private function SET_SUCCESS()
	{
		//$status = &$this->result;
		$status = array_merge($status, array(
			'code'      => 'SUCCESS',
			'message'   => 'Successful API request',
		));
	}

	private function SET_ERROR_SESSION_EXPIRED()
	{
		array_merge($this->result, array(
			'code' 		=> 'ERROR_SESSION_EXPIRED',
			'message'   => 'Session cookie is not valid anymore'
		));
	}

	public function SUCCESS()
	{
		$this->SET_SUCCESS();
		return $this->result;
	}

	public function ERROR_SESSION_EXPIRED()
	{
		$this->SET_ERROR_SESSION_EXPIRED();
		return $this->result;
	}
}