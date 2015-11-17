<?php
/**
 * DB-Restaurant Model Class.
 *
 * @package    Gocci-Mobile
 * @version    3.0 (2015/11/17)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @license    MIT License
 * @copyright  2015 Inase,inc.
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

class Model_V3_Db_Restaurant extends Model_V3_Db
{
	use SingletonTrait;

	/**
	 * @var String $table_name
	 */
	private static $table_name = 'restaurants';

	public function getRestData($rest_id)
	{
		$this->selectData($rest_id);
		$result = $this->run();
		return $result[0];
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

	private function put_rest($rest_name, $lat, $lon)
	{
		$this->query = DB::insert(self::$table_name)
		->set(array(
			'restname' => "$rest_name",
			'lat' 	   => "$lat",
			'lon' 	   => "$lon",
			'lon_lat'  => DB::expr("GeomFromText('POINT(${lon} ${lat})')")
		))
		->execute();

		$this->query = DB::select('rest_id')->from(self::$table_name)
        ->order_by('rest_id', 'desc')
        ->limit   ('1');

        $result  = $query->execute()->as_array();
	}
}