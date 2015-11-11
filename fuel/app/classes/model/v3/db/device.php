<?php
/**
 * Status Code and Message list.
 *
 * @package    Gocci-Mobile
 * @version    3.0 (2015/11/03)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @license    MIT License
 * @copyright  2015 Inase,inc.
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

class Model_V3_Db_Device extends Model_V3_Db
{
    /**
     * @var String $table_name
     */
    private static $table_name = 'devices';


    public function check_arn($register_id)
    {
        $this->select_arn2($register_id);
        $result = $this->run();
        return $result;
    }

    public function get_arn($user_id)
    {
        $this->select_arn($user_id);
        $result = $this->run();
        return $result[0]['endpoint_arn'];
    }

    public function get_user($register_id)
    {
        $this->select_user($register_id);
        $result = $this->run();
        return $result;   
    }

    public function update_data($params)
    {
        $this->update_device($params);
        $result = $this->query->execute();
        return $result;
    }

    public function delete_device($register_id)
    {
        $this->delete_data($register_id);
        $result = $this->query->execute();
        return $result;
    }

    public function set_data($params)
    {
        $this->insert_data($params);
        $result = $this->query->execute();
        return $result;
    }

    //SELECT
    //-------------------------------------------------//

    private function select_id($register_id)
    {
        $this->query = DB::select('device_id')
        ->from(self::$table_name)
        ->where('register_id', "$register_id");
    }

    private function select_arn($user_id)
    {
        $this->query = DB::select('endpoint_arn')
        ->from(self::$table_name)
        ->where('device_user_id', "$user_id");
    }

    private function select_arn2($register_id)
    {
        $this->query = DB::select('endpoint_arn')
        ->from(self::$table_name)
        ->where('register_id', "$register_id");
    }

    private function select_user($register_id)
    {
        $this->query = DB::select('device_user_id')
        ->from(self::$table_name)
        ->where('register_id', "$register_id");
    }

    //UPDATE
    //-------------------------------------------------//

    private function update_device($params)
    {
        $this->query = DB::update(self::$table_name)
        ->set(array(
            'os'            => "$params[os]",
            'model'         => "$params[model]",
            'register_id'   => "$params[register_id]",
            'endpoint_arn'  => "$params[endpoint_arn]"
        ))
        ->where('device_user_id', "$params[user_id]");
    }

    //DELETE
    //-------------------------------------------------//

    private function delete_data($register_id)
    {
        $this->query = DB::delete(self::$table_name)
        ->where('register_id', "$register_id");
    }


    //INSERT
    //-------------------------------------------------//

    private function insert_data($params)
    {
        $this->query = DB::insert(self::$table_name)
        ->set(array(
            'device_user_id'   => "$params[user_id]",
            'os'               => "$params[os]",
            'model'            => "$params[model]",
            'register_id'      => "$params[register_id]",
            'endpoint_arn'     => "$params[endpoint_arn]"
        ));
    }
}