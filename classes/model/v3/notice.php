<?php
/**
 * Notice Model Class.
 *
 * @package    Gocci-Mobile
 * @version    3.0.0 (2015/12/23)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @copyright  (C) 2015 Akira Murata
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
					$sns->type = 'like';
					$notice->type = 'like';
					break;

				case 'comment':
					$sns->type = 'comment';
					$notice->type = 'comment';
					break;

				case 'follow':
					$sns->type = 'follow';
					$notice->type = 'follow';
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


	public function setGochi($params)
	{
		$params['user_id'] = session::get('user_id');
		$this->type = 'like';

		$this->notice->setNotice($params['user_id'], $params['post_user_id'], $params['post_id']);
		$this->push($params['user_id'], $params['post_user_id'], $params['post_id']);
	}


	public function setComment($params)
	{
		$params['user_id'] = session::get('user_id');
		$this->type = 'comment';

		if ($params['user_id'] !== $params['post_user_id'] && empty($params['re_user_id'])) {
			//post_userのみに通知
			$this->push($params['user_id'], $params['post_user_id'], $params['post_id']);
			$this->notice->setNotice($params['user_id'], $params['post_user_id'], $params['post_id']);

		} else if ($params['user_id'] === $params['post_user_id']) {
			//re_userのみに通知
			$re_user_id = explode(',', $params['re_user_id']);
			$num = count($re_user_id);

			for ($i=0; $i < $num; $i++) {
				$this->notice->setNotice($params['user_id'], $re_user_id[$i], $params['post_id']);
				$this->push($params['user_id'], $re_user_id[$i], $params['post_id']);
			}

		} else {
			//両方に通知
			$this->notice->setNotice($params['user_id'], $params['post_user_id'], $params['post_id']);
			$this->push($params['user_id'], $params['post_user_id']);

			$re_user_id = explode(',', $params['re_user_id']);
			$num = count($re_user_id);
			for ($i=0; $i < $num; $i++) {
				$this->notice->setNotice($params['user_id'], $re_user_id[$i], $params['post_id']);
				$this->push($params['user_id'], $re_user_id[$i], $params['post_id']);
			}
		}
	}

	public function setFollow($follow_user_id)
	{
		$this->type = 'follow';

		$this->notice->setNotice(session::get('user_id'), $follow_user_id);
		$this->push(session::get('user_id'), $follow_user_id);
	}

	public function pushPostComplete($user_id)
	{
		$sns = Model_V3_Aws_Sns::getInstance();
		$sns->type = 'post_complete';
		$this->push($user_id, $user_id);
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


	private function push($a_user_id, $p_user_id, $id = 1)
	{
		$user   = Model_V3_Db_User::getInstance();
		$device = Model_V3_Db_Device::getInstance();
		$sns 	= Model_V3_Aws_Sns::getInstance();

		$username    = $user->getName($a_user_id);
		$device_data = $device->getData($p_user_id);

		if (!empty($device_data[0]['endpoint_arn'])) {

			if ($device_data[0]['os'] ===  'android') {
				$sns->pushAndroid($username, $device_data[0]['endpoint_arn'], $id);

			} else if ($device_data[0]['os'] === 'iOS') {
				$sns->pushiOS($username, $device_data[0]['endpoint_arn'], $id);

			} else {
				//Webで利用 通知不必要
			}

		} else {
			error_log("{$p_user_id}に通知できませんでした");
		}
	}
}