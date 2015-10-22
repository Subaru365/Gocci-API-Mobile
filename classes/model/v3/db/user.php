<?php
/**
 * Status Code and Message list.
 *
 * @package    Gocci-Mobile
 * @version    3.0 (2015/10/22)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @license    MIT License
 * @copyright  2015 Inase,inc.
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

/** @return Array $val */
class Model_V3_Db_User extends Model
{
    private static $table_name = 'users';

    /** @var Object $query */
    private $query;


    /** @param String $username */
    private function select_id($username)
    {
        $this->query = DB::select('user_id')
        ->where('username', "$username");
    }

    /** @param String $identity_id */
    private function select_id2($identity_id)
    {
        $this->query = DB::select('user_id')
        ->where('identity_id', "$identity_id");
    }

    /** @param Integer $user_id */
    private function select_name($user_id)
    {
        $query = DB::select('username')
        ->where('user_id', "$user_id");
    }

    /** @param String $username */
    private function select_pass($username)
    {
        $this->query = DB::select('password')
        ->where('username', "$username");
    }

    /** @param String $username */
    private function select_identity($username)
    {
        $this->query = DB::select('identity_id')
        ->where('username', "$username");
    }

    /** @param Integer $user_id */
    private function select_flag($user_id)
    {
        $this->query = DB::select('login_flag')
        ->where('user_id', "$user_id");
    }

    /** @param Integer $user_id */
    private function select_data($user_id)
    {
        $this->query = DB::select('user_id', 'username', 'profile_img')
        ->where('user_id', "$user_id");
    }

    /** @param String $identity_id */
    private function select_auth($identity_id)
    {
        $this->query = DB::select('user_id', 'username', 'identity_id', 'profile_img', 'badge_num')
        ->where('identity_id', "$identity_id");
    }

    // private function select_next_id()
    // {
    //     $query = DB::select('user_id')
    //     ->order_by('user_id', 'desc')
    //     ->limit   ('1');
    // }

    //-------------------------------------------------//

    private function update_name($name)
    {
        $query = DB::update(self::$table_name)
        ->value('profile_img', "$profile_img")
        ->where('user_id', session::get('user_id'));
    }

    private function update_img($profile_img)
    {
        $query = DB::update(self::$table_name)
        ->value('profile_img', "$profile_img")
        ->where('user_id', session::get('user_id'));
    }

    private function update_pass($hash_pass)
    {
        $query = DB::update(self::$table_name)
        ->value('password', "$hash_pass")
        ->where('user_id', session::get('user_id'));
    }

    private function update_facebook()
    {
        $query = DB::update(self::$table_name)
        ->value('facebook_flag', '1')
        ->where('user_id', session::get('user_id'));
    }

    private function update_twitter()
    {
        $query = DB::update(self::$table_name)
        ->value('twitter_flag', '1')
        ->where('user_id', session::get('user_id'));
    }

    private function update_logout($user_id)
    {
        $query = DB::update(self::$table_name)
        ->value('login_flag', '0')
        ->where('user_id', "$user_id");
    }

    private function update_badge()
    {
        $query = DB::update(self::$table_name)
        ->value('badge_num', '0')
        ->where('user_id', session::get('user_id'));
    }

    //-------------------------------------------------//

    private function insert_data($data)
    {
        $query = DB::insert(self::$table_name)
        ->set(array(
            'username'    => "$data[username]",
            'profile_img' => "$data[profile_img]",
            'identity_id' => "$data[identity_id]"
        ));
    }


    //==========================================================================//


    //ユーザー名取得
    private function get_name($user_id)
    {
        $query = DB::select('username')
        ->where('user_id', "$user_id");

        $username = $query->execute()->as_array();
        return $username[0]['username'];
    }



    //通知数取得
    private function get_badge($user_id)
    {
        $query = DB::select('badge_num')
        ->where('user_id', "$user_id");

        $user_id = $query->execute()->as_array();
        return $user_id[0]['badge_num'];
    }

//$profile_img = '0_tosty_' . mt_rand(1, 7);


    //通知数リセット
    private function reset_badge($user_id)
    {
        $query = DB::update(self::$table_name)
        ->value('badge_num', '0')
        ->where('user_id', "$user_id")
        ->execute();

        return $query;
    }


    //SNS連携
    private function update_sns_flag($user_id, $provider)
    {
        if ($provider == 'graph.facebook.com') {
            $flag = 'facebook_flag';
        } else {
            $flag = 'twitter_flag';
        }

        $query = DB::update(self::$table_name)
        ->value("$flag", '1')
        ->where('user_id', "$user_id")
        ->execute();
    }



    //プロフィール画像・ユーザー名変更
    private function update_profile($user_id, $username, $profile_img)
    {
        $query = DB::update(self::$table_name)
        ->value('profile_img', "$profile_img");

        if ($username != '変更に失敗しました') {
            $query->value('username', "$username");
        }

        $query->where('user_id', "$user_id")
        ->execute();

        return $username;
    }



    //SNS連携
    private function delete_sns_flag($user_id, $provider)
    {
        if ($provider == 'graph.facebook.com') {
            $flag = 'facebook_flag';
        } else {
            $flag = 'twitter_flag';
        }

        $query = DB::update(self::$table_name)
        ->value("$flag", '0')
        ->where('user_id', "$user_id")
        ->execute();
    }


    private function encryption_pass($pass)
    {
        $hash_pass = password_hash($pass, PASSWORD_BCRYPT);
        return $hash_pass;
    }


    private function verify_pass($pass, $hash_pass)
    {
        if (password_verify($pass, $hash_pass)) {
            //認証OK
        }else{
            error_log('パスワードが一致しません');
            Controller_V1_Mobile_Base::output_none();
            exit;
        }
    }
}
