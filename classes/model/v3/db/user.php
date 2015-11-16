<?php
/**
 * Status Code and Message list.
 *
 * @package    Gocci-Mobile
 * @version    3.0 (2015/10/29)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @license    MIT License
 * @copyright  2015 Inase,inc.
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

/** @return Array $val */
class Model_V3_Db_User extends Model_V3_Db
{
    use SingletonTrait;
    
    /**
     * @var String $table_name
     */
    private static $table_name = 'users';


    public function check_name($username)
    {
        $this->select_id($username);
        $result = $this->run();
        return $result;
    }

    public function get_id($identity_id)
    {
        $this->select_id2($identity_id);
        $result = $this->run();
        return $result;
    }

    public function get_next_user_id()
    {
        $this->select_last_id();
        $result = $this->run();

        $last_id = $result[0]['user_id'];
        return $last_id++;
    }

    public function get_identity($user_id)
    {
        $this->select_identity($user_id);
        $result = $this->run();
        return $result;
    }

    public function get_password($username)
    {
        $this->select_pass($username);
        $result = $this->run();
        return $result;
    }

    public function getProfile($user_id)
    {
        $this->select_prof($user_id);
        $result = $this->run();
        return $result[0];
    }

    public function get_auth_data($identity_id)
    {
        $this->select_auth($identity_id);
        $result = $this->run();
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

    /** @param String $username */
    private function select_id($username)
    {
        $this->query = DB::select('user_id')
        ->from(self::$table_name)
        ->where('username', "$username");
    }

    /** @param String $identity_id */
    private function select_id2($identity_id)
    {
        $this->query = DB::select('user_id')
        ->from(self::$table_name)
        ->where('identity_id', "$identity_id");
    }

    /** @param Integer $user_id */
    private function select_name($user_id)
    {
        $query = DB::select('username')
        ->from(self::$table_name)
        ->where('user_id', "$user_id");
    }

    /** @param String $username */
    private function select_pass($username)
    {
        $this->query = DB::select('password')
        ->from(self::$table_name)
        ->where('username', "$username");
    }

    /** @param String $username */
    private function select_identity($user_id)
    {
        $this->query = DB::select('identity_id')
        ->from(self::$table_name)
        ->where('user_id', "$user_id");
    }

    private function select_badge($user_id)
    {
        $this->query = DB::select('badge_num')
        ->from(self::$table_name)
        ->where('user_id', "$user_id");
    }

    /** @param Integer $user_id */
    private function select_flag($user_id)
    {
        $this->query = DB::select('login_flag')
        ->from(self::$table_name)
        ->where('user_id', "$user_id");
    }

    /** @param Integer $user_id */
    private function select_prof($user_id)
    {
        $this->query = DB::select('user_id', 'username', 'profile_img')
        ->from(self::$table_name)
        ->where('user_id', "$user_id");
    }

    /** @param String $identity_id */
    private function select_auth($identity_id)
    {
        $this->query = DB::select('user_id', 'username', 'identity_id', 'profile_img', 'badge_num')
        ->from(self::$table_name)
        ->where('identity_id', "$identity_id");
    }

    private function select_last_id()
    {
        $this->query = DB::select('user_id')
        ->from(self::$table_name)
        ->order_by('user_id', 'desc')
        ->limit('1');
    }

    //UPDATE
    //-------------------------------------------------//

    private function update_name($name)
    {
        $this->query = DB::update(self::$table_name)
        ->value('profile_img', "$profile_img")
        ->where('user_id', session::get('user_id'));
    }

    private function update_img($profile_img)
    {
        $this->query = DB::update(self::$table_name)
        ->value('profile_img', "$profile_img")
        ->where('user_id', session::get('user_id'));
    }

    private function update_pass($hash_pass)
    {
        $this->query = DB::update(self::$table_name)
        ->value('password', "$hash_pass")
        ->where('user_id', session::get('user_id'));
    }

    private function update_facebook()
    {
        $this->query = DB::update(self::$table_name)
        ->value('facebook_flag', '1')
        ->where('user_id', session::get('user_id'));
    }

    private function update_twitter()
    {
        $this->query = DB::update(self::$table_name)
        ->value('twitter_flag', '1')
        ->where('user_id', session::get('user_id'));
    }

    private function update_login()
    {
        $this->query = DB::update(self::$table_name)
        ->value('login_flag', '1')
        ->where('user_id', session::get('user_id'));
    }

    private function update_badge($user_id, $badge_num)
    {
        $this->query = DB::update(self::$table_name)
        ->value('badge_num', $badge_num)
        ->where('user_id', $user_id);
    }


    //DELETE
    //-------------------------------------------------//

    private function delete_facebook()
    {
        $this->query = DB::update(self::$table_name)
        ->value('facebook_flag', '0')
        ->where('user_id', session::get('user_id'));
    }

    private function delete_twitter()
    {
        $this->query = DB::update(self::$table_name)
        ->value('twitter_flag', '0')
        ->where('user_id', session::get('user_id'));
    }

    private function delete_login($user_id)
    {
        $this->query = DB::update(self::$table_name)
        ->value('login_flag', '0')
        ->where('user_id', "$user_id");
    }

    private function delete_badge()
    {
        $this->query = DB::update(self::$table_name)
        ->value('badge_num', '0')
        ->where('user_id', session::get('user_id'));
    }


    //INSERT
    //-------------------------------------------------//

    private function insert_data($params)
    {
        $this->query = DB::insert(self::$table_name)
        ->set(array(
            'username'    => "$params[username]",
            'profile_img' => "$params[profile_img]",
            'identity_id' => "$params[identity_id]"
        ));
    }


    //==========================================================================//


    private function encryption_pass($pass)
    {
        $hash_pass = password_hash($pass, PASSWORD_BCRYPT);
        return $hash_pass;
    }
}
