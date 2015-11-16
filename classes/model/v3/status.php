<?php
/**
 * Status Code and Message list.
 *
 * @package    Gocci-Mobile
 * @version    3.0 (2015/11/05)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @license    MIT License
 * @copyright  2015 Inase,inc.
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

class Model_V3_Status extends Model
{
    /**
     * @var Array $api_data
     */
    public $api_params = array();

    /**
     * @var String $code
     */
    private $code = '';

    /**
     * @var String $message
     */
    private $message = '';

    /**
     * @var String $payload
     */
    private $payload = Array();


    private function __construct()
    {
        $this->api_params = array(
            'version'   => 3.0,
            'uri'       => Uri::string(),
            'code'      => $this->code,
            'message'   => $this->message,
            'payload'   => $this->payload
        );
    }

    public static function getInstance()
    {
        static $instance = null;

        if ($instance === null) {
            $instance = new static();
        }
        return $instance;
    }

    public function SUCCESS($payload)
    {
        $this->code     = 'SUCCESS';
        $this->message  = 'Successful API request';
        $this->payload  = "$payload";
        return $this->api_params;
    }

	/**
	 * @param  String $status_code
     * @param  Array  $payload
	 * @return Array  $status
	 */
    public static function getStatus(
        $status_code = 'ERROR_UNKNOWN_ERROR', $payload = array())
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

            case 'ERROR_REQUEST_PARAMETER_INVALID':
                $status['message'] = 'Parameter invalid';
                break;

            case 'ERROR_RESPONSE_PARAMETER_INVALID':
                $status['message'] = 'Parameter invalid';
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

            case 'ERROR_FOLLOW_USER_NOT_EXIST':
                $status['message'] = '';
                break;

    		default:
    			$status['message'] = 'Unknown global error';
    			break;
    	}

    	if ($status_code !== 'SUCCESS') {
    		//View::forge($status);
            error_log($status_code);
    	}

        return $status;
    }
}