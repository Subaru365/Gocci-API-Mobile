<?php
/**
 * Db-Notice Model Class.
 *
 * @package    Gocci-Mobile
 * @version    4.0.0 (2016/1/14)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @copyright  (C) 2016 Akira Murata
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

class Model_V4_Db_Notice extends Model_V4_Db
{
	use SingletonTrait;

	/**
	 * @var String
	 */
	private static $table_name = 'notices';

	/**
	 * @var like OR comment OR follow
	 */
	public $type = '';


	public function getMyNotice()
	{
		$this->selectData(session::get('user_id'));
		$result = $this->run();
		return $result;
	}

	public function setNotice($a_user_id, $p_user_id, $post_id = 1)
	{
		if (!empty($this->type)) {
			$this->insertData($this->type, $a_user_id, $p_user_id, $post_id);
			$this->query->execute();
		} else {
			error_log($this->type);
			exit();
		}
	}


	private function selectData($user_id)
	{
		$this->query = DB::select(
			'notice_id',	'user_id',	'username',
			'profile_img',	'notice',	'notice_post_id',
			'notice_date'
		)
		->from(self::$table_name)

		->join('users', 'INNER')
		->on('notice_a_user_id', '=', 'user_id')

		->join('posts', 'INNER')
		->on('notice_post_id', '=', 'post_id')

		->order_by('notice_date','desc')

		->limit(15)

		->where('notice_p_user_id', $user_id)
		->where('post_status_flag', '1');
    }


    private function insertData($notice, $a_user_id, $p_user_id, $post_id)
    {
    	$this->query = DB::insert(self::$table_name)
    	->set(array(
    		'notice' 			=> $notice,
    		'notice_a_user_id'	=> $a_user_id,
    		'notice_p_user_id'  => $p_user_id,
    		'notice_post_id' 	=> $post_id,
    	));
    }
}