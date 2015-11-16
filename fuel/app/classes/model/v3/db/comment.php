<?php
/**
 * DB-Comment Class.
 *
 * @package    Gocci-Mobile
 * @version    3.0 (2015/11/16)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @license    MIT License
 * @copyright  2015 Inase,inc.
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

/**
 * @return
 */
class Model_V3_Db_Comment extends Model_V3_Db
{
	use SingletonTrait;

	/**
	 * @var String $table_name
	 */
	private static $table_name = 'comments';

	public function getNum($post_id)
	{
		$this->selectId($post_id);
		$result = $this->run();

		$num = count($result);
		return $num;
	}

	//-----------------------------------------------------//

	private function selectId($post_id)
	{
		$this->query = DB::select('comment_id')
		->from(self::$table_name)
		->where('comment_post_id', "$post_id");
	}


	private function get_data($post_id)
	{
		$this->query = DB::select(
			'comment_id', 'comment_user_id', 'username',
			'profile_img', 'comment', 'comment_date')
		->from(self::$table_name)

		->join('users', 'INNER')
		->on('comment_user_id', '=', 'user_id')

		->where('comment_post_id', "$post_id");


		$num = count($comment_data);

		for ($i=0; $i < $num; $i++) {

			$comment_data[$i]['profile_img'] =
				Model_Transcode::decode_profile_img($comment_data[$i]['profile_img']);

			$comment_data[$i]['comment_date'] =
				Model_Date::get_data($comment_data[$i]['comment_date']);
		}

	}


	private function set_data($post_id, $comment)
	{
		$this->query = DB::insert(self::$table_name)
		->set(array(
			'comment_user_id' => session::get('user_id'),
			'comment_post_id' => "$post_id",
			'comment' 	      => "$comment"
		))
		->execute();
	}
}