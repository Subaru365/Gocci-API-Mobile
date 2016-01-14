<?php
/**
 * Device Model Class.
 *
 * @package    Gocci-Mobile
 * @version    4.0.0 (2016/1/14)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @copyright  (C) 2016 Akira Murata
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

class Model_V4_Device extends Model
{
	use SingletonTrait;

	/**
	 * @var Instance $device
	 */
	private $device;

	// Trait Override
	private function __construct()
	{
		$this->device = Model_V4_Db_Device::getInstance();
	}


	public function updateDevice($params)
	{
		$params['user_id'] = session::get('user_id');

		$this->chkDeviceRegisterId($params);
		$this->updateData($params);
	}

	private function chkDeviceRegisterId($params)
	{
		$old_user_id = $this->device->getDeviceUserId($params['device_token']);

        if (!empty($old_user_id)) {
            //以前に端末登録あり
            $old_user_id = $old_user_id[0]['device_user_id'];

            if ($params['user_id'] === $old_user_id) {
                //登録端末情報一致

            } else {
                //別のユーザーから乗り換え
                $old_arn = $this->device->getEndpointArn($old_user_id);
                if (!empty($old_arn)) {
                	$sns = Model_V4_Aws_Sns::getInstance();
                    $sns->deleteSns($old_arn[0]['endpoint_arn']);
                }
                $this->device->deleteDevice($old_user_id);
            }
        }
	}

	private function updateData($params)
	{
		$sns 	= Model_V4_Aws_Sns::getInstance();

		$old_arn = $this->device->getEndpointArn($params['user_id']);

        if (!empty($old_arn)) {
            //端末更新
            $sns->deleteSns($old_arn[0]['endpoint_arn']);
            $params['endpoint_arn'] = $sns->setSns($params);
            $result = $this->device->updateDevice($params);

        } else {
            $params['endpoint_arn'] = $sns->setSns($params);
            $result = $this->device->setDevice($params);
        }
	}
}