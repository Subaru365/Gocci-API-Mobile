<?php
/**
 * User Model Class.
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

	public function getFollowlist($user_id)
	{
		$follow = Model_V3_Db_Follow::getInstance();

		$list = $follow->getFollowData($user_id);
		$list = $this->addData($list);

		return $list;
	}

	public function getFollowerlist($user_id)
	{
		$follow = Model_V3_Db_Follow::getInstance();

		$list = $follow->getFollowerData($user_id);
		$list = $this->addData($list);

		return $list;
	}

	public function setPassword($pass)
	{
		$hash_pass	= $this->encryptionPass($pass);
		$result		= $this->user->setMyPassword($hash_pass);
		return $result;
	}

	public function setSnsLink($params)
	{
		$cognito = Model_V3_Aws_Cognito::getinstance();
		$cognito->setSnsAccount($params);

		if ($params['provider'] === 'graph.facebook.com') {
			$this->user->setFacebookEnable();

		} else {
			$this->user->setTwitterEnable();
		}
	}

	public function setSnsUnlink($params)
	{
		$cognito = Model_V3_Aws_Cognito::getinstance();
		$params['identity_id'] = $this->user->getIdentityIdForId(session::get('user_id'));
		$cognito->unsetSnsAccount($params);

		if ($params['provider'] === 'graph.facebook.com') {
			$this->user->setFacebookDisable();

		} else {
			$this->user->setTwitterDisable();
		}
	}

	//---------------------------------------------------------------//


	private function addData($data)
	{
		$follow = Model_V3_Db_Follow::getInstance();
		$num 	= count($data);

		for ($i=0; $i < $num; $i++) {
			$data[$i]['profile_img'] = Model_V3_Transcode::decode_profile_img($data[$i]['profile_img']);
			$data[$i]['follow_flag'] = $follow->getFollowFlag($data[$i]['user_id']);
		}

		return $data;
	}

	private function encryptionPass($pass)
    {
        $hash_pass = password_hash($pass, PASSWORD_BCRYPT);
        return $hash_pass;
    }
}