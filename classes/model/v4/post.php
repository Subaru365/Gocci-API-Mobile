<?php
/**
 * Post Model Class. It's make posts data.
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
class Model_V4_Post extends Model
{
	use SingletonTrait;

	/**
	 * @var Instance $post
	 */
	private $post;

	// Trait Override
	private function __construct()
	{
		$this->post = Model_V4_Db_Post::getInstance();
	}

	public function getNearline($params)
	{
		$result = $this->post->getNearPostRestId($params);

		if (empty($result)) {
			return '';
		}
		$num = count($result);
		for ($i=0; $i < $num; $i++) {
			$rest_ids[$i] = $result[$i]['post_rest_id'];
		}
		$posts = $this->post->getNearPost($rest_ids, $params);
		
		$num   = count($posts);
		$posts = $this->decodeData($posts, $num);
		$posts = $this->decodeDistance($posts, $num);
		$posts = $this->decodeProfile($posts, $num);
		$posts = $this->decodeDate($posts, $num);
		$posts = $this->addGochiFlag($posts, $num);

		return $posts;
	}

	public function getFollowline($params)
	{
		$posts = $this->post->getFollowPost($params);
		
		$num   = count($posts);
		$posts = $this->decodeData($posts, $num);
		$posts = $this->decodeProfile($posts, $num);
		$posts = $this->decodeDate($posts, $num);
		$posts = $this->addGochiFlag($posts, $num);

		return $posts;
	}

	public function getTimeline($params)
	{
		$posts = $this->post->getTimePost($params);
		
		$num   = count($posts);
		$posts = $this->decodeData($posts, $num);
		$posts = $this->decodeProfile($posts, $num);
		$posts = $this->decodeDate($posts, $num);
		$posts = $this->addGochiFlag($posts, $num);

		return $posts;
	}

	public function getUserPost($user_id)
	{
		$posts = $this->post->getUserPost($user_id);
		
		$num   = count($posts);
		$posts = $this->decodeData($posts, $num);
		$posts = $this->decodeLonLat($posts, $num);
		$posts = $this->cutDate($posts, $num);
		$posts = $this->addStatus($posts, $num);

		return $posts;
	}

	public function getRestPost($rest_id)
	{
		$posts = $this->post->getRestPost($rest_id);
		
		$num   = count($posts);
		$posts = $this->decodeData($posts, $num);
		$posts = $this->decodeProfile($posts, $num);
		$posts = $this->decodeDate($posts, $num);
		$posts = $this->addStatus($posts, $num);

		return $posts;
	}

	public function getMemo($post_id)
	{
		$memo = $this->post->getMemo($post_id);
		$memo = $this->decodeProfile($memo, 1);
		$memo = $this->decodeDate($memo, 1);
		return $memo[0];
	}

	//----------------------------------------------------------------------------//

	private function decodeDistance($data, $num)
	{
		for ($i=0; $i < $num; $i++) {
			$data[$i]['distance'] 		= Model_V4_Transcode::decodeDistance($data[$i]['distance']);
		}
		return $data;
	}

	private function decodeLonLat($data, $num)
	{
		for ($i=0; $i < $num; $i++) { 
			$data[$i] = Model_V4_Transcode::decodeLonLat($data[$i]);
		}
		return $data;
	}

	private function decodeProfile($data, $num)
	{
		for ($i=0; $i < $num; $i++) {
			$data[$i]['profile_img']	= Model_V4_Transcode::decode_profile_img($data[$i]['profile_img']);
		}
		return $data;
	}

	private function decodeData($data, $num)
	{
		for ($i=0; $i < $num; $i++) {
			$data[$i]['mp4_movie'] 		= Model_V4_Transcode::decode_mp4_movie($data[$i]['movie']);
			$data[$i]['hls_movie']   	= Model_V4_Transcode::decode_hls_movie($data[$i]['movie']);
			$data[$i]['thumbnail']   	= Model_V4_Transcode::decode_thumbnail($data[$i]['thumbnail']);
			$data[$i]['cheer_flag']		= Model_V4_Transcode::decodeFlag($data[$i]['cheer_flag']);
		}
		return $data;
	}

	private function decodeDate($data, $num)
	{
		for ($i=0; $i < $num; $i++) {
			$data[$i]['post_date']  	= Model_V4_Transcode::decode_date($data[$i]['post_date']);
		}
		return $data;
	}

	private function cutDate($data, $num)
	{
		for ($i=0; $i < $num; $i++) {
			$data[$i]['post_date']  	= substr($data[$i]['post_date'], 0, 10);
		}
		return $data;
	}

	private function addGochiFlag($data, $num)
	{
		$gochi 		= Model_V4_Db_Gochi::getInstance();
		
		for ($i=0; $i < $num; $i++) {
			$data[$i]['gochi_flag']		= $gochi->getFlag($data[$i]['post_id']);
		}
		return $data;
	}

	private function addStatus($data, $num)
	{
		$gochi 		= Model_V4_Db_Gochi::getInstance();
		$comment 	= Model_V4_Db_Comment::getInstance();

		for ($i=0; $i < $num; $i++) {
			$data[$i]['gochi_num'] 		= $gochi->getNum($data[$i]['post_id']);
			$data[$i]['gochi_flag'] 	= $gochi->getFlag($data[$i]['post_id']);
			$data[$i]['comment_num'] 	= $comment->getNum($data[$i]['post_id']);
		}
		return $data;
	}
}