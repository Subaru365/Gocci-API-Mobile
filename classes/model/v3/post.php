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

	public function getNearline($lon, $lat)
	{
		$posts = $this->post->getNearPost($lon, $lat);
		$posts = $this->decodeDistance($posts);
		$posts = $this->decodeData($posts);
		$posts = $this->addGochiFlag($posts);

		return $posts;
	}

	public function getFollowline($user_id)
	{
		$posts = $this->post->getFollowPost($user_id);
		$posts = $this->decodeData($posts);
		$posts = $this->addGochiFlag($posts);

		return $posts;
	}

	public function getTimeline()
	{
		$posts = $this->post->getTimePost();
		$posts = $this->decodeData($posts);
		$posts = $this->addGochiFlag($posts);

		return $posts;
	}

	public function getUserPost($user_id)
	{
		$posts = $this->post->getUserPost($user_id);
		$posts = $this->decodeData($posts);
		$posts = $this->addStatus($posts);

		return $posts;
	}

	public function getRestPost($rest_id)
	{
		$posts = $this->post->getRestPost($rest_id);
		$posts = $this->decodeData($posts);
		$posts = $this->addStatus($posts);

		return $posts;
	}

	//----------------------------------------------------------------------------//

	private function decodeData($data)
	{
		$post_num  = count($data);

		for ($i=0; $i < $post_num; $i++) {
			$data[$i]['mp4_movie']	= Model_V3_Transcode::decode_mp4_movie($data[$i]['movie']);
			$data[$i]['hls_movie']  = Model_V3_Transcode::decode_hls_movie($data[$i]['movie']);
			$data[$i]['thumbnail']  = Model_V3_Transcode::decode_thumbnail($data[$i]['thumbnail']);
			$data[$i]['post_date']  = Model_V3_Transcode::decode_date($data[$i]['post_date']);
		}
		return $data;
	}

	private function decodeDistance($data)
	{
		$post_num  = count($data);

		for ($i=0; $i < $post_num; $i++) {
			$dis  		= $data[$i]['distance'];
			$dis_meter	= $dis * 112120;
			$data[$i]['distance'] = round($dis_meter);
		}
		return $data;
	}

	private function addGochiFlag($posts)
	{
		$gochi 		= Model_V3_Db_Gochi::getInstance();
		$post_num 	= count($posts);

		for ($i=0; $i < $post_num; $i++) {
			$posts[$i]['gochi_flag'] = $gochi->getFlag($posts[$i]['post_id']);
		}
		return $posts;
	}

	private function addStatus($posts)
	{
		$gochi 		= Model_V3_Db_Gochi::getInstance();
		$comment 	= Model_V3_Db_Comment::getInstance();
		$post_num 	= count($posts);

		for ($i=0; $i < $post_num; $i++) {
			$posts[$i]['gochi_num'] 	= $gochi->getNum($posts[$i]['post_id']);
			$posts[$i]['gochi_flag']    = $gochi->getFlag($posts[$i]['post_id']);
			$posts[$i]['comment_num']   = $comment->getNum($posts[$i]['post_id']);
		}
		return $posts;
	}
}