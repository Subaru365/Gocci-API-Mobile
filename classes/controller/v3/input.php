<?php
/**
 * API first gate. Check RegEx all input parameter.
 *
 * @package    Gocci-Mobile
 * @version    3.0 (2015/10/23)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @license    MIT License
 * @copyright  2015 Inase,inc.
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

class Controller_V3_Input extends Controller
{
    /** @var Array $status*/
    protected $status;

    /** @var Array $parameter */
    protected $params;

    /** @var Array $payload */
    protected $payload;


	public function before()
	{
        $param      = new Model_V3_Param();
        $input_data = $param->get_params();
        
        $validation = new Model_V3_Validation($input_data);
        
        if ($validation->check()) {
        	//Validation Success!

        } else {
        	//Validation Error.
        	$this->status = Model_V3_Status::get_status('VALIDATION_ERROR');
        }
    }
}

 // $time_start = microtime(true);
 // $timelimit  = microtime(true) - $time_start;
 // echo '格納完了：' . $timelimit . ' seconds\r\n';