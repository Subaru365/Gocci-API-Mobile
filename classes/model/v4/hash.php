<?php
/**
 * Authentication Class. Request SignUp, LogIn.
 *
 * @package    Gocci-Mobile
 * @version    4.0.0 (2016/1/14)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @copyright  (C) 2016 Akira Murata
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */
class Model_V4_Hash extends Model
{
    public static function postIdHash($post_id)
    {
        $hash_id 	= `/usr/local/bin/inase-hash/inasehash {$post_id}`;
	    $post_hash  = rtrim($hash_id); // hash_idに含まれる\n 改行を削除
	    return $post_hash;
    }
}
