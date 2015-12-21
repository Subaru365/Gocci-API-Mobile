<?php
/**
 * Authentication Class. Request SignUp, LogIn.
 *
 * @package    Gocci-Mobile
 * @version    3.0 (2015/10/29)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @license    MIT License
 * @copyright  2015 Inase,inc.
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */
class Model_V3_Hash extends Model
{
    public static function postIdHash($post_id)
    {
        $hash_id 	= `/usr/local/bin/inasehash {$post_id}`;
	    $post_hash  = rtrim($hash_id); // hash_idに含まれる\n 改行を削除
	    return $post_hash;
    }
}
