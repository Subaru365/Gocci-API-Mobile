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
class Model_V4_Comment extends Model
{
	use SingletonTrait;

	/**
	 * @var Instance $comment
	 */
	private $comment;

	/**
	 * @var Instance $re
	 */
	private $re;

	// Trait Override
	private function __construct()
	{
		$this->comment 	= Model_V4_Db_Comment::getInstance();
		$this->re 		= Model_V4_Db_Re::getInstance();
	}

	public function getComment($post_id)
	{
		$comment_data = $this->comment->getComment($post_id);
		$comment_data = $this->decodeComment($comment_data);

		return $comment_data;
	}


	private function decodeComment($data)
	{
		$num = count($data);

		for ($i=0; $i < $num; $i++) {
			$data[$i]['profile_img']	= Model_V4_Transcode::decode_profile_img($data[$i]['profile_img']);
			$data[$i]['comment_date'] 	= Model_V4_Transcode::decode_date($data[$i]['comment_date']);
			$data[$i]['re_users'] 		= $this->re->getRe($data[$i]['comment_id']);
		}
		return $data;
	}
}