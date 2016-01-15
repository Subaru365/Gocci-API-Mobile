<?php
/**
 * DB-Block Model Class.
 *
 * @package    Gocci-Mobile
 * @version    4.1.0 (2016/1/15)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @copyright  (C) 2016 Akira Murata
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

class Model_V4_Db_Block extends Model_V4_Db
{
	use SingletonTrait;

	/**
	 * @var String $table_name
	 */
	private static $table_name = 'blocks';


	public function setCommentBlock($comment_id)
	{
		$this->insertCommentData($comment_id);
		$result = $this->query->execute();
		return $result;
	}

	public function setPostBlock($post_id)
	{
		$this->insertPostData($post_id);
		$result = $this->query->execute();
		return $result;
	}

	private function insertCommentData($comment_id)
	{
		$this->query = DB::insert('comment_blocks')
		->set(array(
			'block_user_id' 	=> session::get('user_id'),
			'block_comment_id' 	=> $comment_id,
		));
	}

	private function insertPostData($post_id)
	{
		$this->query = DB::insert('post_blocks')
		->set(array(
			'block_user_id' => session::get('user_id'),
			'block_post_id' => $post_id,
		));
	}
}