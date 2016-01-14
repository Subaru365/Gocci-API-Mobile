<?php
/**
 * DB-Feedback Model Class.
 *
 * @package    Gocci-Mobile
 * @version    4.0.0 (2016/1/14)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @copyright  (C) 2016 Akira Murata
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

/**
 * @return Array
 */
class Model_V4_Db_Feedback extends Model_V4_Db
{
	use SingletonTrait;

	/**
	 * @var String $table_name
	 */
	private static $table_name = 'feedbacks';

	public function setFeedback($feedback)
	{
		$this->insertData($feedback);
		$result = $this->query->execute();
		return $result[0];
	}

	private function insertData($feedback)
	{
		$this->query = DB::insert(self::$table_name)
		->set(array(
			'feedback_user_id' => session::get('user_id'),
			'feedback'		   => $feedback,
		));
	}
}