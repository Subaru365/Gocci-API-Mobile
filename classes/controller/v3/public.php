<?php
/**
 * API first gate. Check RegEx all input parameter.
 *
 * @package    Gocci-Mobile
 * @version    3.0 (2015/10/27)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @license    MIT License
 * @copyright  2015 Inase,inc.
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

class Controller_V3_Public extends Controller
{
    /** 
     * @var Array $status
     */
    protected $status = array();

    /**
     * @var Array $parameter //Get Request Parametar
     */
    protected $req_params = array();

    /**
     * @var Array $res_param //JSON Responce Parametar (api_payload)
     */
    protected $res_params = array();

    /**
     * @var Instance $Param
     */
    private $Param;

    /**
     * @var Instance $User
     */
    private $User;


    //-----------------------------------------------------//


	public function before()
	{
        $this->Param = new Model_V3_Param();

        $this->set_request();

        $this->User = new Model_V3_Db_User();
    }


    protected function output_success()
    {
        $this->set_responce();

        $this->status = Model_V3_Status::get_status('SUCCESS', $res_params);
        $this->output();
    }


    protected function output()
    {
        $json = json_encode(
            $this->status,
            JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|
            JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT
        );

        echo $json;
        exit;
    }


    private function set_request()
    {
        $this->req_params = $this->Param->get_request();
    
        if ($this->req_params)) {
            //Validation Error.
            $status = Model_V3_Status::get_status('VALIDATION_ERROR');
            $this->output;
        }
    }


    private function set_responce()
    {
        $this->res_params = $this->Param->get_responce($this->req_params);
    
        if ($this->res_params)) {
            //Validation Error.
            $status = Model_V3_Status::get_status('VALIDATION_ERROR');
            $this->output;
        }
    }
}

 // $time_start = microtime(true);
 // $timelimit  = microtime(true) - $time_start;
 // echo '格納完了：' . $timelimit . ' seconds\r\n';