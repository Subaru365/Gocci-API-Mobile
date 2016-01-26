<?php
/**
 * Notice Model Class.
 *
 * @package    Gocci-Mobile
 * @version    4.2.0 (2016/1/22)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @copyright  (C) 2016 Akira Murata
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

class Model_V4_Notice extends Model
{
	use SingletonTrait;

	/**
	 * @var Instance $notice
	 */
	private $notice;

	// Trait Override
	private function __construct()
	{
		$this->notice = Model_V4_Db_Notice::getInstance();
	}

	public function __set($name, $val)
	{
		if($name === 'type')
		{
			$sns 	= Model_V4_Aws_Sns::getInstance();
			$notice = Model_V4_Db_Notice::getInstance();

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
		$user = Model_V4_Db_User::getInstance();

		$notice_data = $this->notice->getMyNotice();
		$notice_data = $this->decodeData($notice_data);
		$user->resetBadge();

		return $notice_data;
	}


	public function setGochi($params)
	{
		$this->type = 'like';

		$this->notice->setNotice($params['a_user_id'], $params['p_user_id'], $params['post_id']);
		$this->push($params);
	}


	public function setComment($params)
	{
		$this->type = 'comment';

		if ($params['a_user_id'] != $params['p_user_id'] && !empty($params['re_user_id'])) {
		//両方に通知
			$this->notice->setNotice($params['a_user_id'], $params['p_user_id'], $params['post_id']);
			$this->push($params);

			$re_user_id = explode(',', $params['re_user_id']);
			$num = count($re_user_id);
			for ($i=0; $i < $num; $i++) {
				$params['p_user_id'] = $re_user_id[$i];
				$this->notice->setNotice($params['a_user_id'], $params['p_user_id'], $params['post_id']);
				$this->push($params);
			}


		} else if ($params['a_user_id'] != $params['p_user_id']) {
		//投稿者の通知
			$this->notice->setNotice($params['a_user_id'], $params['p_user_id'], $params['post_id']);
			$this->push($params);


		} else if (!empty($params['re_user_id'])) {
		//返信者に通知
			$re_user_id = explode(',', $params['re_user_id']);
			$num = count($re_user_id);
			for ($i=0; $i < $num; $i++) {
				$params['p_user_id'] = $re_user_id[$i];
				$this->notice->setNotice($params['a_user_id'], $params['p_user_id'], $params['post_id']);
				$this->push($params);
			}

		} else {
		//通知なし
		}
	}

	public function setFollow($params)
	{
		$this->type = 'follow';

		$this->notice->setNotice($params['a_user_id'], $params['p_user_id']);
		$this->push($params);
	}

	public function pushPostComplete($movie)
	{
		$sns = Model_V4_Aws_Sns::getInstance();
		$sns->type = 'post_complete';

		$post   = Model_V4_Db_Post::getInstance();
		$params = $post->getUploadData($movie);
		$params['a_user_id'] = $params['post_user_id'];
		$params['p_user_id'] = $params['post_user_id'];
		$this->push($params);
	}

	public function pushAnnouncement($params)
	{
		$device = Model_V4_Db_Device::getInstance();
		$sns 	= Model_V4_Aws_Sns::getInstance();
		$user   = Model_V4_Db_User::getInstance();

		$sns->type = 'announcement';
		$device_data = $device->getData(1173);

		if (!empty($device_data[0]['endpoint_arn'])) {

			if ($device_data[0]['os'] ===  'android') {
				$sns->pushAndroid($params, $device_data[0]['endpoint_arn']);

			} else if ($device_data[0]['os'] === 'iOS') {
				$params['badge'] = $user->getBadge(1173);
				$sns->pushiOS($params, $device_data[0]['endpoint_arn']);

			} else {
				//Webで利用 通知不必要
			}
		}
	}



	//==================================================//


	private function decodeData($data)
	{
		$num = count($data);

        for ($i=0; $i < $num; $i++) {
          	$data[$i]['notice_date'] = Model_V4_Transcode::decode_date($data[$i]['notice_date']);
            $data[$i]['profile_img'] = Model_V4_Transcode::decode_profile_img($data[$i]['profile_img']);
        }
        return $data;
	}


	private function push($params)
	{
		$user   = Model_V4_Db_User::getInstance();
		$device = Model_V4_Db_Device::getInstance();
		$sns 	= Model_V4_Aws_Sns::getInstance();

		$params['username'] = $user->getName($params['a_user_id']);
		$device_data = $device->getData($params['p_user_id']);

		if (!empty($device_data[0]['endpoint_arn'])) {

			if ($device_data[0]['os'] ===  'android') {
				$sns->pushAndroid($params, $device_data[0]['endpoint_arn']);

			} else if ($device_data[0]['os'] === 'iOS') {
				$params['badge'] = $user->getBadge($params['p_user_id']);
				$sns->pushiOS($params, $device_data[0]['endpoint_arn']);

			} else {
				//Webで利用 通知不必要
			}

		} else {
			error_log("{$params['p_user_id']}に通知できませんでした");
		}
	}
}