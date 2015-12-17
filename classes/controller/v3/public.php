<?php
/**
 * API first gate. Check RegEx all input parameter.
 *
 * @package    Gocci-Mobile
 * @version    3.0.0 (2015/12/09)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @copyright  (C) 2015 Akira Murata
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */
header('Content-Type: application/json; charset=UTF-8');

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
     * @var Instance $User
     */
    protected $User;

    /**
     * @var Instance $Device
     */
    protected $Device;


    //-----------------------------------------------------//


	public function before()
	{
        $this->set_request();
    }

    protected function output_success()
    {
        if (empty($this->res_params)) {
            $this->res_params = json_decode('{}');
        }

        $this->status = Model_V3_Status::getStatus('SUCCESS', $this->res_params);
        $this->output();
    }


    protected function output()
    {
        $json = json_encode(
            $this->status,
            JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|
            JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT
        );

        echo $json;
        exit;
    }


    private function set_request()
    {
        $param  = Model_V3_Param::getInstance();
        $params = $param->get_request();

        if ($params) {
            $this->req_params = $params;

        } else {
            $this->status = Model_V3_Status::getStatus('ERROR_REQUEST_PARAMETER_INVALID');
            $this->output();
        }
    }


    private function set_responce()
    {
        $param  = Model_V3_Param::getInstance();
        $params = $param->get_responce($this->req_params);

        if ($params) {
            $this->res_params = $params;

        } else {
            $this->status = Model_V3_Status::getStatus('ERROR_RESPONSE_PARAMETER_INVALID');
            $this->output();
        }
    }
}

 // $time_start = microtime(true);
 // $timelimit  = microtime(true) - $time_start;
 // echo '格納完了：' . $timelimit . ' seconds\r\n';