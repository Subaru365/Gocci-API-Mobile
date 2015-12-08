<?php

namespace Fuel\Tasks;
use Fuel\Core\DB;

/**
*
*/
class Daily
{

	function run()
	{
		$dau = $this->dau();

		print_r($dau);
	}

	public function dau()
	{
		$query = \DB::select(array('login_user_id', 'user_id'), 'login_date')
		->from('logins')

		->where('login_date', 'between', array(\DB::expr('curdate() - interval 1 day'),  \DB::expr('curdate()')))
		->distinct(true);

		return $query->execute()->as_array();
	}

	public function newUserNum()
	{
		$query = \DB::select(array('login_user_id', 'user_id'), 'login_date')
		->from('logins')

		->where('login_date', 'between', array(\DB::expr('curdate() - interval 1 day'),  \DB::expr('curdate()')))
		->distinct(true);

		return $query->execute()->as_array();
	}

	public function gochiNum()
	{

	}

	public function postNum()
	{

	}

	public function commentNum()
	{

	}

	public function day2RetensionRate()
	{

	}

	public function day4RetensionRate()
	{

	}

	public function day7RetensionRate()
	{

	}
}