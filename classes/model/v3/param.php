
<?php
/**
 * Parameter list of uri.
 *
 * @package    Gocci-Mobile
 * @version    3.0.0 (2015/12/18)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @copyright  (C) 2015 Akira Murata
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

class Model_V3_Param extends Model
{
    use SingletonTrait;

    private $uri_path;

    private $req_params = array();

    public $status = array();


    private function __construct()
    {
        $this->status = array(
            'version'   => 3.7,
            'uri'       => Uri::string(),
            'code'      => '',
            'message'   => '',
            'payload'   => json_decode('{}'),
        );
    }

    public function getRequest($input_params)
    {
        $uri  = substr(Uri::string(), 3);
        $this->uri_path     = str_replace("/", "_", $uri);
        $req_function_name  = "setReqParams_".$this->uri_path;

        $this->$req_function_name($input_params);

        return $this->req_params;
    }

    public function setGlobalCode_SUCCESS($payload)
    {
        if (!empty($payload)) {
            $this->status['payload'] = $payload;
        }
        $this->status['code'] = 'SUCCESS';
        $this->status['message'] = "Successful API request";
    }


    public function setGlobalCode_ERROR_SESSION_EXPIRED()
    {
        $this->status['code'] = 'ERROR_SESSION_EXPIRED';
        $this->status['message'] = "Session cookie is not valid anymore";
    }


    public function setGlobalCode_ERROR_CLIENT_OUTDATED()
    {
        $this->status['code'] = 'ERROR_CLIENT_OUTDATED';
        $this->status['message'] = "The client version is too old for this API. Client update necessary";
    }


    private function setReqParams_auth_login($input_params)
    {
        if(!empty($input_params['identity_id'])) {

            if(preg_match('/^us-east-1:[a-f0-9]{8}(-[a-f0-9]{4}){3}-[a-f0-9]{12}$/', $input_params['identity_id'])) {
                $this->req_params['identity_id'] = $input_params['identity_id'];
            } else {
                $this->status['code']    = 'ERROR_PARAMETER_IDENTITY_ID_MALFORMED';
                $this->status['message'] = "Parameter 'identity_id' is malformed. Should correspond to '^us-east-1:[a-f0-9]{8}(-[a-f0-9]{4}){3}-[a-f0-9]{12}$'";
            }
        }

        else {
            $this->status['code']    = 'ERROR_PARAMETER_IDENTITY_ID_MISSING';
            $this->status['message'] = "Parameter 'identity_id' does not exist.";
        }

    }


    public function set_auth_login_ERROR_IDENTITY_ID_NOT_REGISTERD()
    {
        $this->status['code'] = 'ERROR_IDENTITY_ID_NOT_REGISTERD';
        $this->status['message'] = "The provided identity_id is not bound to any account";
    }


    private function setReqParams_auth_signup($input_params)
    {
        if(!empty($input_params['username'])) {

            if(preg_match('/^[^\p{Cc}]{1,20}$/u', $input_params['username'])) {
                $this->req_params['username'] = $input_params['username'];
            } else {
                $this->status['code']    = 'ERROR_PARAMETER_USERNAME_MALFORMED';
                $this->status['message'] = "Parameter 'username' is malformed. Should correspond to '^[^\p{Cc}]{1,20}$'";
            }
        }

        else {
            $this->status['code']    = 'ERROR_PARAMETER_USERNAME_MISSING';
            $this->status['message'] = "Parameter 'username' does not exist.";
        }

    }


    public function set_auth_signup_ERROR_USERNAME_ALREADY_REGISTERD()
    {
        $this->status['code'] = 'ERROR_USERNAME_ALREADY_REGISTERD';
        $this->status['message'] = "The provided username was already registerd by another user";
    }


    private function setReqParams_auth_password($input_params)
    {
        if(!empty($input_params['username'])) {

            if(preg_match('/^[^\p{Cc}]{1,20}$/u', $input_params['username'])) {
                $this->req_params['username'] = $input_params['username'];
            } else {
                $this->status['code']    = 'ERROR_PARAMETER_USERNAME_MALFORMED';
                $this->status['message'] = "Parameter 'username' is malformed. Should correspond to '^[^\p{Cc}]{1,20}$'";
            }
        }

        else {
            $this->status['code']    = 'ERROR_PARAMETER_USERNAME_MISSING';
            $this->status['message'] = "Parameter 'username' does not exist.";
        }

        if(!empty($input_params['password'])) {

            if(preg_match('/^[^\p{Cc}]{6,25}$/u', $input_params['password'])) {
                $this->req_params['password'] = $input_params['password'];
            } else {
                $this->status['code']    = 'ERROR_PARAMETER_PASSWORD_MALFORMED';
                $this->status['message'] = "Parameter 'password' is malformed. Should correspond to '^[^\p{Cc}]{6,25}$'";
            }
        }

        else {
            $this->status['code']    = 'ERROR_PARAMETER_PASSWORD_MISSING';
            $this->status['message'] = "Parameter 'password' does not exist.";
        }

    }


    public function set_auth_password_ERROR_USERNAME_NOT_REGISTERD()
    {
        $this->status['code'] = 'ERROR_USERNAME_NOT_REGISTERD';
        $this->status['message'] = "The entered username does not exist";
    }


    public function set_auth_password_ERROR_PASSWORD_NOT_REGISTERD()
    {
        $this->status['code'] = 'ERROR_PASSWORD_NOT_REGISTERD';
        $this->status['message'] = "The entered password does not exist";
    }


    public function set_auth_password_ERROR_PASSWORD_WRONG()
    {
        $this->status['code'] = 'ERROR_PASSWORD_WRONG';
        $this->status['message'] = "Password wrong";
    }


    private function setReqParams_set_device($input_params)
    {
        if(!empty($input_params['device_token'])) {

            if(preg_match('/^([a-f0-9]{64})|([a-zA-Z0-9:_-]{140,250})$/', $input_params['device_token'])) {
                $this->req_params['device_token'] = $input_params['device_token'];
            } else {
                $this->status['code']    = 'ERROR_PARAMETER_DEVICE_TOKEN_MALFORMED';
                $this->status['message'] = "Parameter 'device_token' is malformed. Should correspond to '^([a-f0-9]{64})|([a-zA-Z0-9:_-]{140,250})$'";
            }
        }

        else {
            $this->status['code']    = 'ERROR_PARAMETER_DEVICE_TOKEN_MISSING';
            $this->status['message'] = "Parameter 'device_token' does not exist.";
        }

        if(!empty($input_params['os'])) {

            if(preg_match('/^android$|^iOS$/', $input_params['os'])) {
                $this->req_params['os'] = $input_params['os'];
            } else {
                $this->status['code']    = 'ERROR_PARAMETER_OS_MALFORMED';
                $this->status['message'] = "Parameter 'os' is malformed. Should correspond to '^android$|^iOS$'";
            }
        }

        else {
            $this->status['code']    = 'ERROR_PARAMETER_OS_MISSING';
            $this->status['message'] = "Parameter 'os' does not exist.";
        }

        if(!empty($input_params['ver'])) {

            if(preg_match('/^[0-9.]{1,6}$/', $input_params['ver'])) {
                $this->req_params['ver'] = $input_params['ver'];
            } else {
                $this->status['code']    = 'ERROR_PARAMETER_VER_MALFORMED';
                $this->status['message'] = "Parameter 'ver' is malformed. Should correspond to '^[0-9.]{1,6}$'";
            }
        }

        else {
            $this->status['code']    = 'ERROR_PARAMETER_VER_MISSING';
            $this->status['message'] = "Parameter 'ver' does not exist.";
        }

        if(!empty($input_params['model'])) {

            if(preg_match('/^[^\p{Cc}]{1,50}$/u', $input_params['model'])) {
                $this->req_params['model'] = $input_params['model'];
            } else {
                $this->status['code']    = 'ERROR_PARAMETER_MODEL_MALFORMED';
                $this->status['message'] = "Parameter 'model' is malformed. Should correspond to '^[^\p{Cc}]{1,50}$'";
            }
        }

        else {
            $this->status['code']    = 'ERROR_PARAMETER_MODEL_MISSING';
            $this->status['message'] = "Parameter 'model' does not exist.";
        }

    }


    private function setReqParams_unset_device($input_params)
    {
    }


    private function setReqParams_set_password($input_params)
    {
        if(!empty($input_params['password'])) {

            if(preg_match('/^[^\p{Cc}]{6,25}$/u', $input_params['password'])) {
                $this->req_params['password'] = $input_params['password'];
            } else {
                $this->status['code']    = 'ERROR_PARAMETER_PASSWORD_MALFORMED';
                $this->status['message'] = "Parameter 'password' is malformed. Should correspond to '^[^\p{Cc}]{6,25}$'";
            }
        }

        else {
            $this->status['code']    = 'ERROR_PARAMETER_PASSWORD_MISSING';
            $this->status['message'] = "Parameter 'password' does not exist.";
        }

    }


    private function setReqParams_set_sns_link($input_params)
    {
        if(!empty($input_params['provider'])) {

            if(preg_match('/^(api.twitter.com)|(graph.facebook.com)$/', $input_params['provider'])) {
                $this->req_params['provider'] = $input_params['provider'];
            } else {
                $this->status['code']    = 'ERROR_PARAMETER_PROVIDER_MALFORMED';
                $this->status['message'] = "Parameter 'provider' is malformed. Should correspond to '^(api.twitter.com)|(graph.facebook.com)$'";
            }
        }

        else {
            $this->status['code']    = 'ERROR_PARAMETER_PROVIDER_MISSING';
            $this->status['message'] = "Parameter 'provider' does not exist.";
        }

        if(!empty($input_params['sns_token'])) {

            if(preg_match('/^[^\p{Cc}]{20,4000}$/u', $input_params['sns_token'])) {
                $this->req_params['sns_token'] = $input_params['sns_token'];
            } else {
                $this->status['code']    = 'ERROR_PARAMETER_SNS_TOKEN_MALFORMED';
                $this->status['message'] = "Parameter 'sns_token' is malformed. Should correspond to '^[^\p{Cc}]{20,4000}$'";
            }
        }

        else {
            $this->status['code']    = 'ERROR_PARAMETER_SNS_TOKEN_MISSING';
            $this->status['message'] = "Parameter 'sns_token' does not exist.";
        }

    }


    public function set_set_sns_link_ERROR_SNS_PROVIDER_TOKEN_NOT_VALID()
    {
        $this->status['code'] = 'ERROR_SNS_PROVIDER_TOKEN_NOT_VALID';
        $this->status['message'] = "The provided sns token is invalid or has expired";
    }


    public function set_set_sns_link_ERROR_PROVIDER_UNREACHABLE()
    {
        $this->status['code'] = 'ERROR_PROVIDER_UNREACHABLE';
        $this->status['message'] = "The providers server infrastructure appears to be down";
    }


    private function setReqParams_unset_sns_link($input_params)
    {
        if(!empty($input_params['provider'])) {

            if(preg_match('/^(api.twitter.com)|(graph.facebook.com)$/', $input_params['provider'])) {
                $this->req_params['provider'] = $input_params['provider'];
            } else {
                $this->status['code']    = 'ERROR_PARAMETER_PROVIDER_MALFORMED';
                $this->status['message'] = "Parameter 'provider' is malformed. Should correspond to '^(api.twitter.com)|(graph.facebook.com)$'";
            }
        }

        else {
            $this->status['code']    = 'ERROR_PARAMETER_PROVIDER_MISSING';
            $this->status['message'] = "Parameter 'provider' does not exist.";
        }

        if(!empty($input_params['sns_token'])) {

            if(preg_match('/^[^\p{Cc}]{20,4000}$/u', $input_params['sns_token'])) {
                $this->req_params['sns_token'] = $input_params['sns_token'];
            } else {
                $this->status['code']    = 'ERROR_PARAMETER_SNS_TOKEN_MALFORMED';
                $this->status['message'] = "Parameter 'sns_token' is malformed. Should correspond to '^[^\p{Cc}]{20,4000}$'";
            }
        }

        else {
            $this->status['code']    = 'ERROR_PARAMETER_SNS_TOKEN_MISSING';
            $this->status['message'] = "Parameter 'sns_token' does not exist.";
        }

    }


    public function set_unset_sns_link_ERROR_SNS_PROVIDER_TOKEN_NOT_VALID()
    {
        $this->status['code'] = 'ERROR_SNS_PROVIDER_TOKEN_NOT_VALID';
        $this->status['message'] = "The provided sns token is invalid or has expired";
    }


    public function set_unset_sns_link_ERROR_PROVIDER_UNREACHABLE()
    {
        $this->status['code'] = 'ERROR_PROVIDER_UNREACHABLE';
        $this->status['message'] = "The providers server infrastructure appears to be down";
    }


    private function setReqParams_set_gochi($input_params)
    {
        if(!empty($input_params['post_id'])) {

            if(preg_match('/^\d{1,9}$/', $input_params['post_id'])) {
                $this->req_params['post_id'] = $input_params['post_id'];
            } else {
                $this->status['code']    = 'ERROR_PARAMETER_POST_ID_MALFORMED';
                $this->status['message'] = "Parameter 'post_id' is malformed. Should correspond to '^\d{1,9}$'";
            }
        }

        else {
            $this->status['code']    = 'ERROR_PARAMETER_POST_ID_MISSING';
            $this->status['message'] = "Parameter 'post_id' does not exist.";
        }

    }


    private function setReqParams_set_comment($input_params)
    {
        if(!empty($input_params['post_id'])) {

            if(preg_match('/^\d{1,9}$/', $input_params['post_id'])) {
                $this->req_params['post_id'] = $input_params['post_id'];
            } else {
                $this->status['code']    = 'ERROR_PARAMETER_POST_ID_MALFORMED';
                $this->status['message'] = "Parameter 'post_id' is malformed. Should correspond to '^\d{1,9}$'";
            }
        }

        else {
            $this->status['code']    = 'ERROR_PARAMETER_POST_ID_MISSING';
            $this->status['message'] = "Parameter 'post_id' does not exist.";
        }

        if(!empty($input_params['comment'])) {

            if(preg_match('/^(\n|[^\p{Cc}]){1,140}$/u', $input_params['comment'])) {
                $this->req_params['comment'] = $input_params['comment'];
            } else {
                $this->status['code']    = 'ERROR_PARAMETER_COMMENT_MALFORMED';
                $this->status['message'] = "Parameter 'comment' is malformed. Should correspond to '^(\n|[^\p{Cc}]){1,140}$'";
            }
        }

        else {
            $this->status['code']    = 'ERROR_PARAMETER_COMMENT_MISSING';
            $this->status['message'] = "Parameter 'comment' does not exist.";
        }

        if(!empty($input_params['re_user_id'])) {

            if(preg_match('/^[0-9,]{1,9}$/', $input_params['re_user_id'])) {
                $this->req_params['re_user_id'] = $input_params['re_user_id'];
            } else {
                $this->status['code']    = 'ERROR_PARAMETER_RE_USER_ID_MALFORMED';
                $this->status['message'] = "Parameter 're_user_id' is malformed. Should correspond to '^\d{1,9}$'";
            }
        }

        else {
            $this->req_params['re_user_id'] = '';
        }

    }


    private function setReqParams_set_follow($input_params)
    {
        if(!empty($input_params['user_id'])) {

            if(preg_match('/^\d{1,9}$/', $input_params['user_id'])) {
                $this->req_params['user_id'] = $input_params['user_id'];
            } else {
                $this->status['code']    = 'ERROR_PARAMETER_USER_ID_MALFORMED';
                $this->status['message'] = "Parameter 'user_id' is malformed. Should correspond to '^\d{1,9}$'";
            }
        }

        else {
            $this->status['code']    = 'ERROR_PARAMETER_USER_ID_MISSING';
            $this->status['message'] = "Parameter 'user_id' does not exist.";
        }

    }


    private function setReqParams_unset_follow($input_params)
    {
        if(!empty($input_params['user_id'])) {

            if(preg_match('/^\d{1,9}$/', $input_params['user_id'])) {
                $this->req_params['user_id'] = $input_params['user_id'];
            } else {
                $this->status['code']    = 'ERROR_PARAMETER_USER_ID_MALFORMED';
                $this->status['message'] = "Parameter 'user_id' is malformed. Should correspond to '^\d{1,9}$'";
            }
        }

        else {
            $this->status['code']    = 'ERROR_PARAMETER_USER_ID_MISSING';
            $this->status['message'] = "Parameter 'user_id' does not exist.";
        }

    }


    private function setReqParams_set_want($input_params)
    {
        if(!empty($input_params['rest_id'])) {

            if(preg_match('/^\d{1,9}$/', $input_params['rest_id'])) {
                $this->req_params['rest_id'] = $input_params['rest_id'];
            } else {
                $this->status['code']    = 'ERROR_PARAMETER_REST_ID_MALFORMED';
                $this->status['message'] = "Parameter 'rest_id' is malformed. Should correspond to '^\d{1,9}$'";
            }
        }

        else {
            $this->status['code']    = 'ERROR_PARAMETER_REST_ID_MISSING';
            $this->status['message'] = "Parameter 'rest_id' does not exist.";
        }

    }


    private function setReqParams_unset_want($input_params)
    {
        if(!empty($input_params['rest_id'])) {

            if(preg_match('/^\d{1,9}$/', $input_params['rest_id'])) {
                $this->req_params['rest_id'] = $input_params['rest_id'];
            } else {
                $this->status['code']    = 'ERROR_PARAMETER_REST_ID_MALFORMED';
                $this->status['message'] = "Parameter 'rest_id' is malformed. Should correspond to '^\d{1,9}$'";
            }
        }

        else {
            $this->status['code']    = 'ERROR_PARAMETER_REST_ID_MISSING';
            $this->status['message'] = "Parameter 'rest_id' does not exist.";
        }

    }


    private function setReqParams_set_post($input_params)
    {
        if(!empty($input_params['rest_id'])) {

            if(preg_match('/^\d{1,9}$/', $input_params['rest_id'])) {
                $this->req_params['rest_id'] = $input_params['rest_id'];
            } else {
                $this->status['code']    = 'ERROR_PARAMETER_REST_ID_MALFORMED';
                $this->status['message'] = "Parameter 'rest_id' is malformed. Should correspond to '^\d{1,9}$'";
            }
        }

        else {
            $this->status['code']    = 'ERROR_PARAMETER_REST_ID_MISSING';
            $this->status['message'] = "Parameter 'rest_id' does not exist.";
        }

        if(!empty($input_params['movie_name'])) {

            if(preg_match('/^\d{4}(-\d{2}){5}_\d{1,9}$/', $input_params['movie_name'])) {
                $this->req_params['movie_name'] = $input_params['movie_name'];
            } else {
                $this->status['code']    = 'ERROR_PARAMETER_MOVIE_NAME_MALFORMED';
                $this->status['message'] = "Parameter 'movie_name' is malformed. Should correspond to '^[0-9_-]+$'";
            }
        }

        else {
            $this->status['code']    = 'ERROR_PARAMETER_MOVIE_NAME_MISSING';
            $this->status['message'] = "Parameter 'movie_name' does not exist.";
        }

        if(!empty($input_params['category_id'])) {

            if(preg_match('/^\d$/', $input_params['category_id'])) {
                $this->req_params['category_id'] = $input_params['category_id'];
            } else {
                $this->status['code']    = 'ERROR_PARAMETER_CATEGORY_ID_MALFORMED';
                $this->status['message'] = "Parameter 'category_id' is malformed. Should correspond to '^\d$'";
            }
        }

        else {
            $this->req_params['category_id'] = 1;
        }

        if(!empty($input_params['value'])) {

            if(preg_match('/^\d{0,8}$/', $input_params['value'])) {
                $this->req_params['value'] = $input_params['value'];
            } else {
                $this->status['code']    = 'ERROR_PARAMETER_VALUE_MALFORMED';
                $this->status['message'] = "Parameter 'value' is malformed. Should correspond to '^\d{0,8}$'";
            }
        }

        else {
            $this->req_params['value'] = 0;
        }

        if(!empty($input_params['memo'])) {

            if(preg_match('/^\S{1,140}$/', $input_params['memo'])) {
                $this->req_params['memo'] = $input_params['memo'];
            } else {
                $this->status['code']    = 'ERROR_PARAMETER_MEMO_MALFORMED';
                $this->status['message'] = "Parameter 'memo' is malformed. Should correspond to '^\S{1,140}$'";
            }
        }

        else {
            $this->req_params['memo'] = 'none';
        }

        if(!empty($input_params['cheer_flag'])) {

            if(preg_match('/^0$|^1$/', $input_params['cheer_flag'])) {
                $this->req_params['cheer_flag'] = $input_params['cheer_flag'];
            } else {
                $this->status['code']    = 'ERROR_PARAMETER_CHEER_FLAG_MALFORMED';
                $this->status['message'] = "Parameter 'cheer_flag' is malformed. Should correspond to '^0$|^1$'";
            }
        }

        else {
            $this->req_params['cheer_flag'] = 0;
        }
    }


    private function setReqParams_unset_post($input_params)
    {
        if(!empty($input_params['post_id'])) {

            if(preg_match('/^\d{1,9}$/', $input_params['post_id'])) {
                $this->req_params['post_id'] = $input_params['post_id'];
            } else {
                $this->status['code']    = 'ERROR_PARAMETER_POST_ID_MALFORMED';
                $this->status['message'] = "Parameter 'post_id' is malformed. Should correspond to '^\d{1,9}$'";
            }
        }

        else {
            $this->status['code']    = 'ERROR_PARAMETER_POST_ID_MISSING';
            $this->status['message'] = "Parameter 'post_id' does not exist.";
        }

    }


    private function setReqParams_set_post_block($input_params)
    {
        if(!empty($input_params['post_id'])) {

            if(preg_match('/^\d{1,9}$/', $input_params['post_id'])) {
                $this->req_params['post_id'] = $input_params['post_id'];
            } else {
                $this->status['code']    = 'ERROR_PARAMETER_POST_ID_MALFORMED';
                $this->status['message'] = "Parameter 'post_id' is malformed. Should correspond to '^\d{1,9}$'";
            }
        }

        else {
            $this->status['code']    = 'ERROR_PARAMETER_POST_ID_MISSING';
            $this->status['message'] = "Parameter 'post_id' does not exist.";
        }

    }


    private function setReqParams_set_username($input_params)
    {
        if(!empty($input_params['username'])) {

            if(preg_match('/^[^\p{Cc}]{1,20}$/u', $input_params['username'])) {
                $this->req_params['username'] = $input_params['username'];
            } else {
                $this->status['code']    = 'ERROR_PARAMETER_USERNAME_MALFORMED';
                $this->status['message'] = "Parameter 'username' is malformed. Should correspond to '^[^\p{Cc}]{1,20}$'";
            }
        }

        else {
            $this->status['code']    = 'ERROR_PARAMETER_USERNAME_MISSING';
            $this->status['message'] = "Parameter 'username' does not exist.";
        }

    }


    public function set_set_username_ERROR_USERNAME_ALREADY_REGISTERD()
    {
        $this->status['code'] = 'ERROR_USERNAME_ALREADY_REGISTERD';
        $this->status['message'] = "The provided username was already registerd by another user";
    }


    private function setReqParams_set_profile_img($input_params)
    {
        if(!empty($input_params['profile_img'])) {

            if(preg_match('/^[0-9_-]+_img$/', $input_params['profile_img'])) {
                $this->req_params['profile_img'] = $input_params['profile_img'];
            } else {
                $this->status['code']    = 'ERROR_PARAMETER_PROFILE_IMG_MALFORMED';
                $this->status['message'] = "Parameter 'profile_img' is malformed. Should correspond to '^[0-9_-]+_img$'";
            }
        }

        else {
            $this->status['code']    = 'ERROR_PARAMETER_PROFILE_IMG_MISSING';
            $this->status['message'] = "Parameter 'profile_img' does not exist.";
        }

    }


    private function setReqParams_set_feedback($input_params)
    {
        if(!empty($input_params['feedback'])) {

            if(preg_match('/^[^\p{Cc}]{1,10000}$/u', $input_params['feedback'])) {
                $this->req_params['feedback'] = $input_params['feedback'];
            } else {
                $this->status['code']    = 'ERROR_PARAMETER_FEEDBACK_MALFORMED';
                $this->status['message'] = "Parameter 'feedback' is malformed. Should correspond to '^[^\p{Cc}]{1,10000}$'";
            }
        }

        else {
            $this->status['code']    = 'ERROR_PARAMETER_FEEDBACK_MISSING';
            $this->status['message'] = "Parameter 'feedback' does not exist.";
        }

    }


    private function setReqParams_set_rest($input_params)
    {
        if(!empty($input_params['restname'])) {

            if(preg_match('/^[^\p{Cc}]{1,80}$/u', $input_params['restname'])) {
                $this->req_params['restname'] = $input_params['restname'];
            } else {
                $this->status['code']    = 'ERROR_PARAMETER_RESTNAME_MALFORMED';
                $this->status['message'] = "Parameter 'restname' is malformed. Should correspond to '^[^\p{Cc}]{1,80}$'";
            }
        }

        else {
            $this->status['code']    = 'ERROR_PARAMETER_RESTNAME_MISSING';
            $this->status['message'] = "Parameter 'restname' does not exist.";
        }

        if(!empty($input_params['lat'])) {

            if(preg_match('/^-?\d{1,3}.\d{1,20}$/', $input_params['lat'])) {
                $this->req_params['lat'] = $input_params['lat'];
            } else {
                $this->status['code']    = 'ERROR_PARAMETER_LAT_MALFORMED';
                $this->status['message'] = "Parameter 'lat' is malformed. Should correspond to '^\d{1,3}.\d{1,20}$'";
            }
        }

        else {
            $this->status['code']    = 'ERROR_PARAMETER_LAT_MISSING';
            $this->status['message'] = "Parameter 'lat' does not exist.";
        }

        if(!empty($input_params['lon'])) {

            if(preg_match('/^-?\d{1,3}.\d{1,20}$/', $input_params['lon'])) {
                $this->req_params['lon'] = $input_params['lon'];
            } else {
                $this->status['code']    = 'ERROR_PARAMETER_LON_MALFORMED';
                $this->status['message'] = "Parameter 'lon' is malformed. Should correspond to '^\d{1,3}.\d{1,20}$'";
            }
        }

        else {
            $this->status['code']    = 'ERROR_PARAMETER_LON_MISSING';
            $this->status['message'] = "Parameter 'lon' does not exist.";
        }

    }


    private function setReqParams_get_nearline($input_params)
    {
        if(!empty($input_params['lat'])) {

            if(preg_match('/^-?\d{1,3}\.\d{1,20}$/', $input_params['lat'])) {
                $this->req_params['lat'] = $input_params['lat'];
            } else {
                $this->status['code']    = 'ERROR_PARAMETER_LAT_MALFORMED';
                $this->status['message'] = "Parameter 'lat' is malformed. Should correspond to '^\d{1,3}\.\d{1,20}$'";
            }
        }

        else {
            $this->status['code']    = 'ERROR_PARAMETER_LAT_MISSING';
            $this->status['message'] = "Parameter 'lat' does not exist.";
        }

        if(!empty($input_params['lon'])) {

            if(preg_match('/^-?\d{1,3}\.\d{1,20}$/', $input_params['lon'])) {
                $this->req_params['lon'] = $input_params['lon'];
            } else {
                $this->status['code']    = 'ERROR_PARAMETER_LON_MALFORMED';
                $this->status['message'] = "Parameter 'lon' is malformed. Should correspond to '^\d{1,3}\.\d{1,20}$'";
            }
        }

        else {
            $this->status['code']    = 'ERROR_PARAMETER_LON_MISSING';
            $this->status['message'] = "Parameter 'lon' does not exist.";
        }

        if(!empty($input_params['page'])) {

            if(preg_match('/^\d{1,9}$/', $input_params['page'])) {
                $this->req_params['page'] = $input_params['page'];
            } else {
                $this->status['code']    = 'ERROR_PARAMETER_PAGE_MALFORMED';
                $this->status['message'] = "Parameter 'page' is malformed. Should correspond to '^\d{1,9}$'";
            }
        }

        if(!empty($input_params['category_id'])) {

            if(preg_match('/^\d{1,9}$/', $input_params['category_id'])) {
                $this->req_params['category_id'] = $input_params['category_id'];
            } else {
                $this->status['code']    = 'ERROR_PARAMETER_CATEGORY_ID_MALFORMED';
                $this->status['message'] = "Parameter 'category_id' is malformed. Should correspond to '^\d{1,9}$'";
            }
        }

        if(!empty($input_params['value_id'])) {

            if(preg_match('/^\d{1,9}$/', $input_params['value_id'])) {
                $this->req_params['value_id'] = $input_params['value_id'];
            } else {
                $this->status['code']    = 'ERROR_PARAMETER_VALUE_ID_MALFORMED';
                $this->status['message'] = "Parameter 'value_id' is malformed. Should correspond to '^\d{1,9}$'";
            }
        }

    }


    private function setReqParams_get_followline($input_params)
    {
        if(!empty($input_params['page'])) {

            if(preg_match('/^\d{1,9}$/', $input_params['page'])) {
                $this->req_params['page'] = $input_params['page'];
            } else {
                $this->status['code']    = 'ERROR_PARAMETER_PAGE_MALFORMED';
                $this->status['message'] = "Parameter 'page' is malformed. Should correspond to '^\d{1,9}$'";
            }
        }

        if(!empty($input_params['category_id'])) {

            if(preg_match('/^\d{1,9}$/', $input_params['category_id'])) {
                $this->req_params['category_id'] = $input_params['category_id'];
            } else {
                $this->status['code']    = 'ERROR_PARAMETER_CATEGORY_ID_MALFORMED';
                $this->status['message'] = "Parameter 'category_id' is malformed. Should correspond to '^\d{1,9}$'";
            }
        }

        if(!empty($input_params['value_id'])) {

            if(preg_match('/^\d{1,9}$/', $input_params['value_id'])) {
                $this->req_params['value_id'] = $input_params['value_id'];
            } else {
                $this->status['code']    = 'ERROR_PARAMETER_VALUE_ID_MALFORMED';
                $this->status['message'] = "Parameter 'value_id' is malformed. Should correspond to '^\d{1,9}$'";
            }
        }

    }


    private function setReqParams_get_timeline($input_params)
    {
        if(!empty($input_params['page'])) {

            if(preg_match('/^\d{1,9}$/', $input_params['page'])) {
                $this->req_params['page'] = $input_params['page'];
            } else {
                $this->status['code']    = 'ERROR_PARAMETER_PAGE_MALFORMED';
                $this->status['message'] = "Parameter 'page' is malformed. Should correspond to '^\d{1,9}$'";
            }
        }

        if(!empty($input_params['category_id'])) {

            if(preg_match('/^\d{1,9}$/', $input_params['category_id'])) {
                $this->req_params['category_id'] = $input_params['category_id'];
            } else {
                $this->status['code']    = 'ERROR_PARAMETER_CATEGORY_ID_MALFORMED';
                $this->status['message'] = "Parameter 'category_id' is malformed. Should correspond to '^\d{1,9}$'";
            }
        }

        if(!empty($input_params['value_id'])) {

            if(preg_match('/^\d{1,9}$/', $input_params['value_id'])) {
                $this->req_params['value_id'] = $input_params['value_id'];
            } else {
                $this->status['code']    = 'ERROR_PARAMETER_VALUE_ID_MALFORMED';
                $this->status['message'] = "Parameter 'value_id' is malformed. Should correspond to '^\d{1,9}$'";
            }
        }

    }


    private function setReqParams_get_user($input_params)
    {
        if(!empty($input_params['user_id'])) {

            if(preg_match('/^\d{1,9}$/', $input_params['user_id'])) {
                $this->req_params['user_id'] = $input_params['user_id'];
            } else {
                $this->status['code']    = 'ERROR_PARAMETER_USER_ID_MALFORMED';
                $this->status['message'] = "Parameter 'user_id' is malformed. Should correspond to '^\d{1,9}$'";
            }
        }

        else {
            $this->status['code']    = 'ERROR_PARAMETER_USER_ID_MISSING';
            $this->status['message'] = "Parameter 'user_id' does not exist.";
        }

    }


    private function setReqParams_get_rest($input_params)
    {
        if(!empty($input_params['rest_id'])) {

            if(preg_match('/^\d{1,9}$/', $input_params['rest_id'])) {
                $this->req_params['rest_id'] = $input_params['rest_id'];
            } else {
                $this->status['code']    = 'ERROR_PARAMETER_REST_ID_MALFORMED';
                $this->status['message'] = "Parameter 'rest_id' is malformed. Should correspond to '^\d{1,9}$'";
            }
        }

        else {
            $this->status['code']    = 'ERROR_PARAMETER_REST_ID_MISSING';
            $this->status['message'] = "Parameter 'rest_id' does not exist.";
        }

    }


    private function setReqParams_get_post($input_params)
    {
        if(!empty($input_params['post_id'])) {

            if(preg_match('/^\d{1,9}$/', $input_params['post_id'])) {
                $this->req_params['post_id'] = $input_params['post_id'];
            } else {
                $this->status['code']    = 'ERROR_PARAMETER_POST_ID_MALFORMED';
                $this->status['message'] = "Parameter 'post_id' is malformed. Should correspond to '^\d{1,9}$'";
            }
        }

        else {
            $this->status['code']    = 'ERROR_PARAMETER_POST_ID_MISSING';
            $this->status['message'] = "Parameter 'post_id' does not exist.";
        }

    }


    private function setReqParams_get_comment($input_params)
    {
        if(!empty($input_params['post_id'])) {

            if(preg_match('/^\d{1,9}$/', $input_params['post_id'])) {
                $this->req_params['post_id'] = $input_params['post_id'];
            } else {
                $this->status['code']    = 'ERROR_PARAMETER_POST_ID_MALFORMED';
                $this->status['message'] = "Parameter 'post_id' is malformed. Should correspond to '^\d{1,9}$'";
            }
        }

        else {
            $this->status['code']    = 'ERROR_PARAMETER_POST_ID_MISSING';
            $this->status['message'] = "Parameter 'post_id' does not exist.";
        }

    }


    private function setReqParams_get_follow($input_params)
    {
        if(!empty($input_params['user_id'])) {

            if(preg_match('/^\d{1,9}$/', $input_params['user_id'])) {
                $this->req_params['user_id'] = $input_params['user_id'];
            } else {
                $this->status['code']    = 'ERROR_PARAMETER_USER_ID_MALFORMED';
                $this->status['message'] = "Parameter 'user_id' is malformed. Should correspond to '^\d{1,9}$'";
            }
        }

        else {
            $this->status['code']    = 'ERROR_PARAMETER_USER_ID_MISSING';
            $this->status['message'] = "Parameter 'user_id' does not exist.";
        }

    }


    private function setReqParams_get_follower($input_params)
    {
        if(!empty($input_params['user_id'])) {

            if(preg_match('/^\d{1,9}$/', $input_params['user_id'])) {
                $this->req_params['user_id'] = $input_params['user_id'];
            } else {
                $this->status['code']    = 'ERROR_PARAMETER_USER_ID_MALFORMED';
                $this->status['message'] = "Parameter 'user_id' is malformed. Should correspond to '^\d{1,9}$'";
            }
        }

        else {
            $this->status['code']    = 'ERROR_PARAMETER_USER_ID_MISSING';
            $this->status['message'] = "Parameter 'user_id' does not exist.";
        }

    }


    private function setReqParams_get_want($input_params)
    {
        if(!empty($input_params['user_id'])) {

            if(preg_match('/^\d{1,9}$/', $input_params['user_id'])) {
                $this->req_params['user_id'] = $input_params['user_id'];
            } else {
                $this->status['code']    = 'ERROR_PARAMETER_USER_ID_MALFORMED';
                $this->status['message'] = "Parameter 'user_id' is malformed. Should correspond to '^\d{1,9}$'";
            }
        }

        else {
            $this->status['code']    = 'ERROR_PARAMETER_USER_ID_MISSING';
            $this->status['message'] = "Parameter 'user_id' does not exist.";
        }

    }


    private function setReqParams_get_user_cheer($input_params)
    {
        if(!empty($input_params['user_id'])) {

            if(preg_match('/^\d{1,9}$/', $input_params['user_id'])) {
                $this->req_params['user_id'] = $input_params['user_id'];
            } else {
                $this->status['code']    = 'ERROR_PARAMETER_USER_ID_MALFORMED';
                $this->status['message'] = "Parameter 'user_id' is malformed. Should correspond to '^\d{1,9}$'";
            }
        }

        else {
            $this->status['code']    = 'ERROR_PARAMETER_USER_ID_MISSING';
            $this->status['message'] = "Parameter 'user_id' does not exist.";
        }

    }


    private function setReqParams_get_rest_cheer($input_params)
    {
        if(!empty($input_params['rest_id'])) {

            if(preg_match('/^\d{1,9}$/', $input_params['rest_id'])) {
                $this->req_params['rest_id'] = $input_params['rest_id'];
            } else {
                $this->status['code']    = 'ERROR_PARAMETER_REST_ID_MALFORMED';
                $this->status['message'] = "Parameter 'rest_id' is malformed. Should correspond to '^\d{1,9}$'";
            }
        }

        else {
            $this->status['code']    = 'ERROR_PARAMETER_REST_ID_MISSING';
            $this->status['message'] = "Parameter 'rest_id' does not exist.";
        }

    }


    private function setReqParams_get_notice($input_params)
    {
    }


    private function setReqParams_get_near($input_params)
    {
        if(!empty($input_params['lat'])) {

            if(preg_match('/^\d{1,3}.\d{1,20}$/', $input_params['lat'])) {
                $this->req_params['lat'] = $input_params['lat'];
            } else {
                $this->status['code']    = 'ERROR_PARAMETER_LAT_MALFORMED';
                $this->status['message'] = "Parameter 'lat' is malformed. Should correspond to '^\d{1,3}.\d{1,20}$'";
            }
        }

        else {
            $this->status['code']    = 'ERROR_PARAMETER_LAT_MISSING';
            $this->status['message'] = "Parameter 'lat' does not exist.";
        }

        if(!empty($input_params['lon'])) {

            if(preg_match('/^\d{1,3}.\d{1,20}$/', $input_params['lon'])) {
                $this->req_params['lon'] = $input_params['lon'];
            } else {
                $this->status['code']    = 'ERROR_PARAMETER_LON_MALFORMED';
                $this->status['message'] = "Parameter 'lon' is malformed. Should correspond to '^\d{1,3}.\d{1,20}$'";
            }
        }

        else {
            $this->status['code']    = 'ERROR_PARAMETER_LON_MISSING';
            $this->status['message'] = "Parameter 'lon' does not exist.";
        }

    }


    private function setReqParams_get_heatmap($input_params)
    {
    }


}

