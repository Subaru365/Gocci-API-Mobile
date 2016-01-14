<?php
/**
 * DB-Restaurant Model Class.
 *
 * @package    Gocci-Mobile
 * @version    4.0.0 (2016/1/14)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @copyright  (C) 2016 Akira Murata
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

class Model_V4_Db_Restaurant extends Model_V4_Db
{
	use SingletonTrait;

	/**
	 * @var String $table_name
	 */
	private static $table_name = 'restaurants';


	public function getNearData($lon, $lat)
	{
		$this->selectNear($lon, $lat);
		$result = $this->run();
		return $result;
	}


	public function getRestData($rest_id)
	{
		$this->selectData($rest_id);
		$result = $this->run();
		return $result[0];
	}

	public function setRestData($params)
	{
		$this->insertData($params);
		$result = $this->query->execute();
		return $result[0];
	}


	private function selectNear($lon, $lat)
	{
		$this->query = DB::select('rest_id', 'restname')
		->from(self::$table_name)
		->order_by(DB::expr("GLength(GeomFromText(CONCAT('LineString(${lon} ${lat},', X(lon_lat),' ', Y(lon_lat),')')))"))
		->limit(30);
	}

	private function selectData($rest_id)
	{
		$this->query = DB::select(
			'rest_id',	'restname',	'locality',
			'lat',		'lon', 		'tell',
			'homepage', 'rest_category'
		)
		->from(self::$table_name)
		->where('rest_id', $rest_id);
	}

	private function insertData($data)
	{
		$this->query = DB::insert(self::$table_name)
		->set(array(
			'restname' => $data['restname'],
			'lon_lat'  => DB::expr("GeomFromText('POINT({$data['lon']} {$data['lat']})')"),
		));
	}
}