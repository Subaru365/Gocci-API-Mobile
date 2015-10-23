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

class Controller_V3_Status extends Controller
{
	/**
	 * @param  String $status_code
	 * @return Array  $status 
	 */
    public static function get_status($status_code)
    {
    	$status = array(
			'version'   => 3.0,
			'uri'       => Uri::string()
		);

    	switch ($status_code) {

    		case 'SUCCESS':
    			$status += array(
    				'code'		=> 'SUCCESS',
    				'message'	=> 'Successful API request'
    			);
    			break;

    		case 'ERROR_CONNECTION_FAILED':
    			$status += array(
    				'code'		=> 'ERROR_CONNECTION_FAILED',
    				'message'	=> 'Server connection failed'
    			);
    			break;

    		case 'ERROR_USERNAME_ALREADY_REGISTERD':
    			$status += array(
    				'code'		=> 'ERROR_USERNAME_ALREADY_REGISTERD',
    				'message'	=> 'The provided username was already registerd by another user'
    			);
    			break;

    		case 'ERROR_REGISTER_ID_ALREADY_REGISTERD':
				$status += array(
    				'code'		=> 'ERROR_REGISTER_ID_ALREADY_REGISTERD',
    				'message'	=> 'This deviced already has an registerd account'
    			);
    			break;

    		case 'ERROR_IDENTITY_ID_NOT_REGISTERD':
    			$status += array(
    				'code'		=> 'ERROR_IDENTITY_ID_NOT_REGISTERD',
    				'message'	=> 'The provided identity_id is not bound to any account'
    			);
    			break;

    		case 'ERROR_USERNAME_NOT_REGISTERD':
    			$status += array(
    				'code'		=> 'ERROR_USERNAME_NOT_REGISTERD',
    				'message'	=> 'The entered username does not exist'
    			);
    			break;

    		case 'ERROR_PASSWORD_WRONG':
    			$status += array(
    				'code'		=> 'ERROR_PASSWORD_WRONG',
    				'message'	=> 'Password wrong'
    			);
    			break;

    		default:
    			$status += array(
    				'code'		=> 'ERROR_UNKNOWN_ERROR',
    				'message'	=> 'Unknown global error'
    			);
    			break;
    	}

    	if ($status_code !== 'SUCCESS') {
    		View::forge($status);
    	}
    }
}