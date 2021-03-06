<?php

class Model_V1_Transcode extends Model
{
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
}