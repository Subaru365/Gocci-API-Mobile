<?php
/**
 * Status Code and Message list.
 *
 * @package    Gocci-Mobile
 * @version    3.0 (2015/10/26)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @license    MIT License
 * @copyright  2015 Inase,inc.
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

class Model_V3_Status extends Model
{
	/**
	 * @param  String $status_code
     * @param  Array  $payload
	 * @return Array  $status 
	 */
    public static function get_status($status_code, $payload = array())
    {
    	$status = array(
			'version'   => 3.0,
			'uri'       => Uri::string(),
            'code'      => $status_code,
            'payload'   => $payload
		);

    	switch ($status_code) {

    		case 'SUCCESS':
    			$status['message'] = 'Successful API request';
    			break;

    		case 'ERROR_CONNECTION_FAILED':
    			$status['message'] = 'Server connection failed';
    			break;

    		case 'ERROR_USERNAME_ALREADY_REGISTERD':
    			$status['message'] = 'The provided username was already registerd by another user';
    			break;

    		case 'ERROR_REGISTER_ID_ALREADY_REGISTERD':
				$status['message'] = 'This deviced already has an registerd account';
    			break;

    		case 'ERROR_IDENTITY_ID_NOT_REGISTERD':
    			$status['message'] = 'The provided identity_id is not bound to any account';
    			break;

    		case 'ERROR_USERNAME_NOT_REGISTERD':
    			$status['message'] = 'The entered username does not exist';
    			break;

    		case 'ERROR_PASSWORD_WRONG':
    			$status['message'] = 'Password wrong';
    			break;

    		default:
    			$status['message'] = 'Unknown global error';
    			break;
    	}

    	if ($status_code !== 'SUCCESS') {
    		//View::forge($status);
            error_log('NOT SUCCESS');
            exit;
    	}

        return $status;
    }
}