<?php
/**
 * Get  Class. Request SignUp, LogIn.
 *
 * @package    Gocci-Mobile
 * @version    3.0 (2015/11/16)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @license    MIT License
 * @copyright  2015 Inase,inc.
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

class Model_V3_User extends Model
{
	use SingletonTrait;

	/**
	 * @var Instance $user
	 */
	private $user;

	// Trait Override
	private function __construct()
	{
		$this->user = Model_V3_Db_User::getInstance();
	}

	public function getProfile($user_id)
	{
		$follow 	= Model_V3_Db_Follow::getInstance();
		$post   	= Model_V3_Db_Post::getInstance();
		$want   	= Model_V3_Db_Want::getInstance();

		$profile 	= $this->user->getProfile($user_id);
		
		$profile['profile_img'] 	= Model_V3_Transcode::decode_profile_img($profile['profile_img']);

		$profile['follow_num'] 		= $follow->getFollowNum($user_id);
        $profile['follower_num'] 	= $follow->getFollowerNum($user_id);
        $profile['follow_flag']   	= $follow->getFollowFlag($user_id);
        $profile['cheer_num']     	= $post->getUserCheerNum($user_id);
        $profile['want_num']      	= $want->getWantNum($user_id);

        return $profile;
	}
}