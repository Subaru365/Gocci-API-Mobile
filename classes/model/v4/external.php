<?php
/**
 * External Model Class.
 *
 * @package    Gocci-Mobile
 * @version    4.1.0 (2016/1/15)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @copyright  (C) 2016 Akira Murata
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */
class Model_V4_External extends Model
{
    public static function getPostHashId($post_id)
    {
        $hash_id 	= `/usr/local/bin/inase-hash/inasehash {$post_id}`;
	    $post_hash  = rtrim($hash_id); // hash_idに含まれる\n 改行を削除
	    return $post_hash;
    }

    public static function getAddress($lon. $lat)
    {
    	$json = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?language=ja&latlng='.$lat.','.$lon.'&sensor=false');
		$data = json_decode($json, true);
		$address = substr($data['results'][0]['formatted_address'], 20));
		return $address;
    }
}
