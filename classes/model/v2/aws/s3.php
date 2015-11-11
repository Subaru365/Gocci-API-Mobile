<?php
use Aws\S3\S3Client;

/**
* S3 Model
*/
class Model_V2_Aws_S3 extends Model
{
    public static function input($profile_img_url)
    {
        $bucket = Config::get('_s3.Bucket');

        $i = rand(1, 10);
        $code = 'wget -O /tmp/img/' . "$i" . '.png ' . "$profile_img_url";
        exec("$code");

        $put_name = session::get('user_id') . '_' . date("Y-m-d-H-i-s") . '.png';

        $client = new S3Client([
            'region'    => 'ap-northeast-1',
            'version'   => '2006-03-01'
        ]);

        $result = $client->putObject([
            'Bucket'        => "$bucket",
            'Key'           => "$put_name",
            'SourceFile'    => '/tmp/img/' . "$i" . '.png',
        ]);

        $name = explode('.', $put_name);
        return $name[0];
    }
}