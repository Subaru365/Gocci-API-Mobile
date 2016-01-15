<?php
use Aws\Sns\SnsClient;
/**
*
*/
class Controller_Test extends Controller
{
	public function action_index()
	{
		$json = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?language=ja&latlng=35.6585746,139.6973335&sensor=false');
		$data = json_decode($json, true);
		$address = substr($data['results'][0]['formatted_address'], 20));
	}
}