<?php
/**
 * Notice Model Class.
 *
 * @package    Gocci-Mobile
 * @version    3.0 (2015/11/20)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @license    MIT License
 * @copyright  2015 Inase,inc.
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

class Model_V3_Notice extends Model
{
	use SingletonTrait;

	/**
	 * @var Instance $notice
	 */
	private $notice;

	// Trait Override
	private function __construct()
	{
		$this->notice = Model_V3_Db_Notice::getInstance();
	}

	public function __set($name, $val)
	{
		if($name === 'type')
		{
			$sns 	= Model_V3_Aws_Sns::getInstance();
			$notice = Model_V3_Db_Notice::getInstance();

			switch ($val) {
				case 'like':
					$sns->message = 'like';
					$notice->type = 'いいね！';
					break;

				case 'comment':
					$sns->message = 'comment';
					$notice->type = 'コメント';
					break;

				case 'follow':
					$sns->message = 'follow';
					$notice->type = 'フォロー';
					break;
				
				default:
					exit();
					break;
			}
		} else {
			exit();
		}
	}

	public function getNotice()
	{
		$user = Model_V3_Db_User::getInstance();

		$notice_data = $this->notice->getMyNotice();
		$notice_data = $this->decodeData($notice_data);
		$user->resetBadge();

		return $notice_data;
	}


	public function pushGochi($params)
	{
		$sns = Model_V3_Aws_Sns::getInstance();
		$sns->type = 'like';
		$this->push($params['user_id'], $params['post_user_id']);

		$this->notice->type = 'like';
		$this->notice->setNotice($params);
	}


	public function pushComment($params)
	{
		$sns = Model_V3_Aws_Sns::getInstance();
		$sns->type = 'comment';
		$this->push($params['user_id'], $params['post_user_id']);

		$this->notice->type = 'comment';
		$this->notice->setNotice($params);


		if (empty($params['re_user_id'])) {
			//post_userのみに通知
			$this->push($params['user_id'], $params['post_user_id']);

		} else if (empty($params['post_user_id'])) {
			//re_userのみに通知
			$num = count($params['re_user_id']);
			for ($i=0; $i < $num; $i++) {
				$this->push($params['user_id'], $params['re_user_id'][$i]);
			}

		} else {
			//両方に通知
			$this->push($params['user_id'], $params['post_user_id']);

			$num = count($params['re_user_id']);
			for ($i=0; $i < $num; $i++) {
				$this->push($params['user_id'], $params['re_user_id'][$i]);
			}
		}
	}

	public function pushFollow($params)
	{
		$this->type = 'follow';

		$this->push($params['user_id'], $params['follow_user_id']);
		$this->notice->setNotice($params['user_id'], $params['follow_user_id']);
	}



	//==================================================//


	private function decodeData($data)
	{
		$num = count($data);

        for ($i=0; $i < $num; $i++) {
          	$data[$i]['notice_date'] = Model_V3_Transcode::decode_date($data[$i]['notice_date']);
            $data[$i]['profile_img'] = Model_V3_Transcode::decode_profile_img($data[$i]['profile_img']);
        }
        return $data;
	}


	private function push($a_user_id, $p_user_id)
	{
		$user   = Model_V3_Db_User::getInstance();
		$device = Model_V3_Db_Device::getInstance();
		$sns 	= Model_V3_Aws_Sns::getInstance();

		$username    = $user->getName($a_user_id);
		$device_data = $device->getData($p_user_id);

		if ($device_data['os'] ===  'android') {
			$sns->pushAndroid($username, $device_data['endpoint_arn']);

		} else if ($device_data['os'] === 'iOS') {
			$sns->pushiOS($username, $device_data['endpoint_arn']);

		} else {
			//Webで利用 通知不必要
		}
	}
}