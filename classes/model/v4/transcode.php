<?php
/**
 * Transcode Model Class.
 *
 * @package    Gocci-Mobile
 * @version    4.0.0 (2016/1/14)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @copyright  (C) 2016 Akira Murata
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

class Model_V4_Transcode extends Model
{
    public static function encodePostName($post_data)
    {
        $directory = explode('-', $post_data['movie_name']);
        $post_data['thumbnail']  = $directory[0] . '/' . $directory[1] . '/'  . '00002_' . $post_data['movie_name'] . '_img';
        $post_data['movie']      = $directory[0] . '/' . $directory[1] . '/'  . $post_data['movie_name'] . '_movie';
        return $post_data;
    }

    public static function decode_profile_img($profile_img)
    {
        $img_url = Config::get('_url.img');
        $profile_img = "$img_url" . "$profile_img" . '.png';
        return $profile_img;
    }

    public static function decode_thumbnail($thumbnail)
    {
        $thumbnail_url = Config::get('_url.thumbnail');
        $thumbnail = "$thumbnail_url" . "$thumbnail" . '.png';
        return $thumbnail;
    }

    public static function decode_hls_movie($movie)
    {
        $movie_url = Config::get('_url.hls_movie');
        $movie = "$movie_url" . "$movie" . '.m3u8';
        return $movie;
    }

    public static function decode_mp4_movie($movie)
    {
        $movie_url = Config::get('_url.mp4_movie');
        $movie = "$movie_url" . "$movie" . '.mp4';
        return $movie;
    }

    public static function decodeDistance($value)
    {
        $distance = $value * 112120;
        $distance = round($distance, 0);
        return $distance;
    }

    public static function decodeFlag($data)
    {
        if ($data === '1') {
            $flag = true;
        }else{
            $flag = false;
        }
        return $flag;
    }

    public static function decodeLonLat($data)
    {
        $data['lon'] = (float)$data['lon'];
        $data['lat'] = (float)$data['lat'];
        return $data;
    }

    //TIMESTAMPから現在までの差分を求める
    public static function decode_date($date)
    {
        $datetime1 = new DateTime("$date");
        $datetime2 = new DateTime(date('Y-m-d H:i:s'));

        $interval = $datetime1->diff($datetime2);

        if ($interval->format('%y') > 0) {
            $date_diff = $interval->format('%y') . '年前';

        }elseif ($interval->format('%m') > 0) {
            $date_diff = $interval->format('%m') . 'ヶ月前';

        }elseif ($interval->format('%d') > 0) {
            $date_diff = $interval->format('%d') . '日前';

        }elseif ($interval->format('%h') > 0) {
            $date_diff = $interval->format('%h') . '時間前';

        }elseif ($interval->format('%i') > 0) {
            $date_diff = $interval->format('%i') . '分前';

        }elseif ($interval->format('%s') >= 0) {
            $date_diff = $interval->format('%s') . '秒前';

        }else{
            $date_diff = '未来から';
            error_log('$post_dateの時刻エラー');
        }

        return $date_diff;
    }
}