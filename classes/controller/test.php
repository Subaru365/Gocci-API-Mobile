<?php
use Aws\Sns\SnsClient;
/**
*
*/
class Controller_Test extends Controller
{

	public function action_index()
	{
		$api   = preg_split("/[ \/]/", 'GocciTest/iOS/2.1.1 API/4.1 (iPhone 6/9.2.1)');
		echo $api[4];
	}
}