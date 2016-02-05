<?php
/**
 * User Model Class.
 *
 * @package    Gocci-Mobile
 * @version    4.2.1 (2016/1/26)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @copyright  (C) 2016 Akira Murata
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

class Model_V4_User extends Model
{
	use SingletonTrait;

	/**
	 * @var Instance $user
	 */
	private $user;

	// Trait Override
	private function __construct()
	{
		$this->user = Model_V4_Db_User::getInstance();
	}

	public function getProfile($user_id)
	{
		$follow 	= Model_V4_Db_Follow::getInstance();
		$gochi		= Model_V4_Db_Gochi::getInstance();
		$post   	= Model_V4_Db_Post::getInstance();

		$data 	= $this->user->getProfileForId($user_id);

		$data['profile_img'] 	= Model_V4_Transcode::decode_profile_img($data['profile_img']);

		$data['follow_num'] 	= $follow->getFollowNum($user_id);
        $data['follower_num'] 	= $follow->getFollowerNum($user_id);
        $data['follow_flag']   	= $follow->getFollowFlag($user_id);
        $data['post_num']   	= $post->getNumForUser($user_id);
        $data['gochi_num']   	= $gochi->getNumForUser($user_id);
        $data['cheer_num']     	= $post->getUserCheerNum($user_id);

        return $data;
	}

	public function getUsername($username)
	{
		$gochi = Model_V4_Db_Gochi::getInstance();

		$data = $this->user->getProfileForName($username);
		$data = $this->addData($data);

		return $data;
	}

	public function getFollowlist($user_id)
	{
		$follow = Model_V4_Db_Follow::getInstance();

		$list = $follow->getFollowData($user_id);
		$list = $this->addData($list);

		return $list;
	}

	public function getFollowerlist($user_id)
	{
		$follow = Model_V4_Db_Follow::getInstance();

		$list = $follow->getFollowerData($user_id);
		$list = $this->addData($list);

		return $list;
	}

	public function getFollowerRank($page)
	{
		$follow = Model_V4_Db_Follow::getInstance();
		$post   = Model_V4_Db_Post::getInstance();

		$rank = $follow->getFollowerRank($page);
		if (empty($rank)) {
			return '';
		}
		for ($i=0; $i < 10; $i++) {
			$data[$i] = $this->user->getProfileForId($rank[$i]['follow_p_user_id']);
		}
		$data = $this->addData($data);
		return $data;
	}

	public function setPassword($pass)
	{
		$hash_pass	= $this->encryptionPass($pass);
		$result		= $this->user->setMyPassword($hash_pass);
		return $result;
	}

	public function setSnsLink($params)
	{
		$cognito = Model_V4_Aws_Cognito::getinstance();
		$params['identity_id'] = $this->user->getIdentityIdForId(session::get('user_id'));
		$cognito->setSnsAccount($params);

		if ($params['provider'] === 'graph.facebook.com') {
			$this->user->setFacebookEnable();

		} else {
			$this->user->setTwitterEnable();
		}
	}

	public function setSnsUnlink($params)
	{
		$cognito = Model_V4_Aws_Cognito::getinstance();
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
		$gochi  = Model_V4_Db_Gochi::getInstance();
		$follow = Model_V4_Db_Follow::getInstance();
		$num 	= count($data);

		for ($i=0; $i < $num; $i++) {
			$data[$i]['profile_img'] = Model_V4_Transcode::decode_profile_img($data[$i]['profile_img']);
			$data[$i]['gochi_num']	 = $gochi->getNumForUser($data[$i]['user_id']);
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