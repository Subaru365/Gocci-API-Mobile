<?php
/**
 * Status Code and Message list.
 *
 * @package    Gocci-Mobile
 * @version    3.0 (2015/11/18)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @license    MIT License
 * @copyright  2015 Inase,inc.
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

class Model_V3_Db_Device extends Model_V3_Db
{
    use SingletonTrait;

    /**
     * @var String $table_name
     */
    private static $table_name = 'devices';


    public function check_arn($device_token)
    {
        $this->select_arn2($device_token);
        $result = $this->run();
        return $result;
    }

    public function getEndpointArn($user_id)
    {
        $this->selectArn($user_id);
        $result = $this->run();
        return $result;
    }

    public function getDeviceUserId($device_token)
    {
        $this->selectUserId($device_token);
        $result = $this->run();
        return $result;
    }

    public function getData($user_id)
    {
        try {
            $this->selectData($user_id);
            $result = $this->run();
            return $result[0];
        }
        catch (Exception $e){
            error_log($e);
            exit;
        }
    }

    public function updateDevice($params)
    {
        $this->updateData($params);
        $result = $this->query->execute();
        return $result;
    }

    public function deleteDevice($user_id)
    {
        $this->deleteData($user_id);
        $result = $this->query->execute();
        return $result;
    }

    public function setDevice($params)
    {
        $this->insertData($params);
        $result = $this->query->execute();
        return $result[0];
    }


    //SELECT
    //-------------------------------------------------//

    private function select_id($device_token)
    {
        $this->query = DB::select('device_id')
        ->from(self::$table_name)
        ->where('register_id', "$device_token");
    }

    private function selectArn($user_id)
    {
        $this->query = DB::select('endpoint_arn')
        ->from(self::$table_name)
        ->where('device_user_id', $user_id);
    }

    private function select_arn2($device_token)
    {
        $this->query = DB::select('endpoint_arn')
        ->from(self::$table_name)
        ->where('register_id', "$device_token");
    }

    private function selectUserId($device_token)
    {
        $this->query = DB::select('device_user_id')
        ->from(self::$table_name)
        ->where('register_id', "$device_token");
    }

    private function selectData($user_id)
    {
        $this->query = DB::select('os', 'endpoint_arn')
        ->from(self::$table_name)
        ->where('device_user_id', $user_id);
    }


    //UPDATE
    //-------------------------------------------------//

    private function updateData($params)
    {
        $this->query = DB::update(self::$table_name)
        ->set(array(
            'os'            => $params['os'],
            'ver'           => $params['ver'],
            'model'         => $params['model'],
            'register_id'   => $params['device_token'],
            'endpoint_arn'  => $params['endpoint_arn'],
        ))
        ->where('device_user_id', $params['user_id']);
    }


    //DELETE
    //-------------------------------------------------//

    private function deleteData($user_id)
    {
        $this->query = DB::delete(self::$table_name)
        ->where('device_user_id', $user_id);
    }


    //INSERT
    //-------------------------------------------------//

    private function insertData($params)
    {
        $this->query = DB::insert(self::$table_name)
        ->set(array(
            'device_user_id'    => $params['user_id'],
            'os'                => $params['os'],
            'ver'               => $params['ver'],
            'model'             => $params['model'],
            'register_id'       => $params['device_token'],
            'endpoint_arn'      => $params['endpoint_arn'],
        ));
    }
}