<?php
/**
 * API first gate. Check RegEx all input parameter.
 *
 * @package    Gocci-Mobile
 * @version    3.0 (2015/10/21)
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
    protected $parameter;

    /** @var Array $payload */
    protected $payload;


	public function before()
	{
        $input_data         = array_merge(Input::get(), Input::post());
		
        $parameter          = new Model_V3_Validation($input_data);
        $this->parameter    = $parameter;

        $status             = new Model_V3_Status();
        //$this->status       = $status->SUCCESS();
    }
}

 // $time_start = microtime(true);
 // $timelimit  = microtime(true) - $time_start;
 // echo '格納完了：' . $timelimit . ' seconds\r\n';