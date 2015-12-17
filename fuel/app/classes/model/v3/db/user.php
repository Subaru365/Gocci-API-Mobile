<?php
/**
 * Status Code and Message list.
 *
 * @package    Gocci-Mobile
 * @version    3.0.0 (2015/12/17)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @copyright  (C) 2015 Akira Murata
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


    public function getIdForName($username)
    {
        $this->selectIdForName($username);
        $result = $this->run();
        return $result;
    }

    public function get_id($identity_id)
    {
        $this->select_id2($identity_id);
        $result = $this->run();
        return $result;
    }

    public function getNextId()
    {
        $this->selectLastUserId();
        $result = $this->run();

        $user_id = $result[0]['user_id'];
        $user_id++;
        return $user_id;
    }

    public function getIdentityID($username)
    {
        $this->selectIdentityId($username);
        $result = $this->run();
        return $result[0]['identity_id'];
    }

    public function getIdentityIdForId($user_id)
    {
        $this->selectIdentityIdForId($user_id);
        $result = $this->run();
        return $result[0]['identity_id'];
    }


    public function getPassword($username)
    {
        $this->selectPassword($username);
        $result = $this->run();
        return $result;
    }

    public function getName($user_id)
    {
        $this->selectName($user_id);
        $result = $this->run();
        return $result[0]['username'];
    }

    public function getProfile($user_id)
    {
        $this->select_prof($user_id);
        $result = $this->run();
        return $result[0];
    }

    public function getUser($identity_id)
    {
        $this->selectData($identity_id);
        $result = $this->run();
        return $result;
    }

    public function setMyName($username)
    {
        $this->updateName(session::get('user_id'), $username);
        $result = $this->query->execute();
        return $result;
    }

    public function setMyProfileImg($profile_img)
    {
        $this->updateProfileImg(session::get('user_id'), $profile_img);
        $result = $this->query->execute();
        return $result;
    }

    public function setMyPassword($hash_pass)
    {
        $this->updatePassword(session::get('user_id'), $hash_pass);
        $result = $this->query->execute();
        return $result;
    }

    public function resetBadge()
    {
        $this->updateBadge(session::get('user_id'), 0);
        $this->query->execute();
    }

    public function setFacebookEnable()
    {
        $this->updateFacebookFlag(1);
        $this->query->execute();
    }

    public function setTwitterEnable()
    {
        $this->updateTwitterFlag(1);
        $this->query->execute();
    }

    public function setFacebookDisable()
    {
        $this->updateFacebookFlag(0);
        $this->query->execute();
    }

    public function setTwitterDisable()
    {
        $this->updateTwitterFlag(0);
        $this->query->execute();
    }


    public function setData($params)
    {
        $this->insertData($params);
        $result = $this->query->execute();
        return $result[0];
    }


    //SELECT
    //-------------------------------------------------//

    /** @param String $username */
    private function selectIdForName($username)
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
    private function selectName($user_id)
    {
        $this->query = DB::select('username')
        ->from(self::$table_name)
        ->where('user_id', $user_id);
    }

    /** @param String $username */
    private function selectPassword($username)
    {
        $this->query = DB::select('password')
        ->from(self::$table_name)
        ->where('username', $username);
    }

    /** @param String $username */
    private function selectIdentityId($username)
    {
        $this->query = DB::select('identity_id')
        ->from(self::$table_name)
        ->where('username', $username);
    }

    /** @param String $username */
    private function selectIdentityIdForId($user_id)
    {
        $this->query = DB::select('identity_id')
        ->from(self::$table_name)
        ->where('user_id', $user_id);
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
        $this->query = DB::select(
            'user_id', 'username', 'profile_img'
        )
        ->from(self::$table_name)
        ->where('user_id', "$user_id");
    }

    /** @param String $identity_id */
    private function selectData($identity_id)
    {
        $this->query = DB::select(
            'user_id',      'username',     'identity_id',
            'profile_img',  'badge_num'
        )
        ->from(self::$table_name)
        ->where('identity_id', $identity_id);
    }

    private function selectLastUserId()
    {
        $this->query = DB::select('user_id')
        ->from(self::$table_name)
        ->order_by('user_id', 'desc')
        ->limit('1');
    }

    //UPDATE
    //-------------------------------------------------//

    private function updateName($id, $name)
    {
        $this->query = DB::update(self::$table_name)
        ->value('username', $name)
        ->where('user_id', $id);
    }

    private function updateProfileImg($id, $profile_img)
    {
        $this->query = DB::update(self::$table_name)
        ->value('profile_img', $profile_img)
        ->where('user_id', $id);
    }

    private function update_img($profile_img)
    {
        $this->query = DB::update(self::$table_name)
        ->value('profile_img', "$profile_img")
        ->where('user_id', session::get('user_id'));
    }

    private function updatePassword($id, $password)
    {
        $this->query = DB::update(self::$table_name)
        ->value('password', "$password")
        ->where('user_id', $id);
    }

    private function updateFacebookFlag($flag)
    {
        $this->query = DB::update(self::$table_name)
        ->value('facebook_flag', $flag)
        ->where('user_id', session::get('user_id'));
    }

    private function updateTwitterFlag($flag)
    {
        $this->query = DB::update(self::$table_name)
        ->value('twitter_flag', $flag)
        ->where('user_id', session::get('user_id'));
    }

    private function update_login()
    {
        $this->query = DB::update(self::$table_name)
        ->value('login_flag', '1')
        ->where('user_id', session::get('user_id'));
    }

    private function updateBadge($id, $badge_num)
    {
        $this->query = DB::update(self::$table_name)
        ->value('badge_num', $badge_num)
        ->where('user_id', $id);
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

    private function delete_login($id)
    {
        $this->query = DB::update(self::$table_name)
        ->value('login_flag', '0')
        ->where('user_id', "$id");
    }

    private function delete_badge()
    {
        $this->query = DB::update(self::$table_name)
        ->value('badge_num', '0')
        ->where('user_id', session::get('user_id'));
    }


    //INSERT
    //-------------------------------------------------//

    private function insertData($params)
    {
        $this->query = DB::insert(self::$table_name)
        ->set(array(
            'username'    => "$params[username]",
            'profile_img' => "$params[profile_img]",
            'identity_id' => "$params[identity_id]",
        ));
    }
}
