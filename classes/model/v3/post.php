<?php
/**
 * Post Model Class. Request SignUp, LogIn.
 *
 * @package    Gocci-Mobile
 * @version    3.0 (2015/11/16)
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
	use SingletonTrait;

	/**
	 * @var Instance $post
	 */
	private $post;

	// Trait Override
	private function __construct()
	{
		$this->post = Model_V3_Db_Post::getInstance();
	}

	public function getUserPost($user_id)
	{
		$gochi 		= Model_V3_Db_Gochi::getInstance();
		$comment 	= Model_V3_Db_Comment::getInstance();
		$want 		= Model_V3_Db_Want::getInstance();

		$posts 	= $this->post->getUserPost($user_id);

		$post_num  = count($posts);

		for ($i=0; $i < $post_num; $i++) {
			$posts[$i]['gochi_num'] 	= $gochi->getNum($posts[$i]['post_id']);
			$posts[$i]['gochi_flag']    = $gochi->getFlag($posts[$i]['post_id']);
			$posts[$i]['comment_num']   = $comment->getNum($posts[$i]['post_id']);
			//$post[$i]['want_flag']      = $want->getFlag($post[$i]['rest_id']);
		}
		return $posts;
	}
}