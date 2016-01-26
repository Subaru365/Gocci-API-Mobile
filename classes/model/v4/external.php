<?php
/**
 * External Model Class.
 *
 * @package    Gocci-Mobile
 * @version    4.1.1 (2016/1/20)
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

    public static function getAddress($lon, $lat)
    {
    	$json = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?language=ja&latlng='.$lat.','.$lon.'&sensor=false');
		$data = json_decode($json, true);

        if (!empty($data['results'][0]['formatted_address'])) {
            $address = substr($data['results'][0]['formatted_address'], 20);
            return $address;
        } else {
            return '不明';
        }
    }

    public static function blockAlert($id, $category)
    {
        $url        = Config::get('_slack.webhook_url');
        $payload    = array(
            'text'       => "*Block*\n  {$category}ID : {$id}",
            'icon_emoji' => ':warning:',
        );
        self::slackAlert($url, $payload);
    }

    public static function feedbackAlert($feedback)
    {
        $url        = Config::get('_slack.webhook_url');
        $payload    = array(
            'text'       => "*Feedback*\n```$feedback```",
            'icon_emoji' => ':loudspeaker:',
        );
        self::slackAlert($url, $payload);
    }

    private static function slackAlert($url, $data)
    {
        $headers    = array();
        $params     = array('payload' => json_encode($data));

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $result = curl_exec($ch);
        $error  = curl_error($ch);

        curl_close($ch);
        if ($error) {
            error_log($error);
        }
    }
}
