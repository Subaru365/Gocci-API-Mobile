<?php
/**
 * API first gate. Check RegEx all input parameter.
 *
 * @package    Gocci-Mobile
 * @version    3.1.0 (2015/12/17)
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


    //-----------------------------------------------------//


	public function before()
	{
        $this->setRequest();
    }


    protected function outputSuccess()
    {
        $param = Model_V3_Param::getInstance();
        $param->setGlobalCode_SUCCESS($this->res_params);
        $this->output();
    }


    protected function output()
    {
        $param = Model_V3_Param::getInstance();
        $status = $param->status;

        $json = json_encode(
            $status,
            JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|
            JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_QUOT
        );

        echo $json;
        exit;
    }


    private function setRequest()
    {
        $param  = Model_V3_Param::getInstance();
        $params = $param->getRequest(input::get());

        if (!empty($param->status['code'])) {
            $this->output();

        } else {
            $this->req_params = $params;
        }
    }
}

 // $time_start = microtime(true);
 // $timelimit  = microtime(true) - $time_start;
 // echo '格納完了：' . $timelimit . ' seconds\r\n';
