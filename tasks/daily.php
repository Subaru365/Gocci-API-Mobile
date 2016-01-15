<?php
/**
 * Daily Cron Code.
 *
 * @package    Gocci-Mobile
 * @version    3.1.0 (2016/1/14)
 * @author     Subaru365 (a-murata@inase-inc.jp)
 * @copyright  (C) 2015 Akira Murata
 * @link       https://bitbucket.org/inase/gocci-mobile-api
 */

namespace Fuel\Tasks;
use Fuel\Core\DB;
\Package::load('email');

/**
*
*/
class Daily
{

	private $message = '';

	function run()
	{
		$dau 		= $this->dau();
		$newUser 	= $this->newUser();
		$gochi 		= $this->gochi();
		$post 		= $this->post();
		$comment 	= $this->comment();

		$day2RR 	= $this->dayXRetensionRate(2);
		$day3RR 	= $this->dayXRetensionRate(3);
		$day5RR 	= $this->dayXRetensionRate(5);
		$day7RR 	= $this->dayXRetensionRate(7);
		$day14RR 	= $this->dayXRetensionRate(14);

		$num = array(
			'dau'		=> count($dau),
			'newUser' 	=> count($newUser),
			'gochi'		=> count($gochi),
			'post'		=> count($post),
			'comment' 	=> count($comment),

			'day2RR' 	=> count($day2RR),
			'day3RR' 	=> count($day3RR),
			'day5RR' 	=> count($day5RR),
			'day7RR' 	=> count($day7RR),
			'day14RR' 	=> count($day14RR),
		);

		$this->setMessage($num);

		$this->sendEmail();
		$this->sendSlack();
	}

	private function dau()
	{
		$query = \DB::select('login_user_id')
		->from('logins')
		->join('users', 'INNER')
		->on('login_user_id', '=', 'user_id')

		->where('login_date', 'between', array(\DB::expr('curdate() - interval 1 day'),  \DB::expr('curdate()')))
		->and_where('attribute', 'general')
		->distinct(true);

		return $query->execute()->as_array();
	}

	private function newUser()
	{
		$query = \DB::select('user_id', 'user_date')
		->from('users')
		->where('user_date', 'between', array(\DB::expr('curdate() - interval 1 day'),  \DB::expr('curdate()')))
		->and_where('attribute', 'general');

		return $query->execute()->as_array();
	}

	private function gochi()
	{
		$query = \DB::select('gochi_user_id', 'gochi_post_id', 'gochi_date')
		->from('gochis')
		->join('users', 'INNER')
		->on('gochi_user_id', '=', 'user_id')

		->where('gochi_date', 'between', array(\DB::expr('curdate() - interval 1 day'),  \DB::expr('curdate()')))
		->and_where('attribute', 'general');

		return $query->execute()->as_array();
	}

	private function post()
	{
		$query = \DB::select('post_id', 'post_user_id', 'post_date')
		->from('posts')
		->join('users', 'INNER')
		->on('post_user_id', '=', 'user_id')

		->where('post_date', 'between', array(\DB::expr('curdate() - interval 1 day'),  \DB::expr('curdate()')))
		->and_where('attribute', 'general');

		return $query->execute()->as_array();
	}

	private function comment()
	{
		$query = \DB::select('comment_id', 'comment_user_id', 'comment_post_id', 'comment_date')
		->from('comments')
		->join('users', 'INNER')
		->on('comment_user_id', '=', 'user_id')

		->where('comment_date', 'between', array(\DB::expr('curdate() - interval 1 day'),  \DB::expr('curdate()')))
		->and_where('attribute', 'general');

		return $query->execute()->as_array();
	}

	private function dayXRetensionRate($day)
	{
		$yday = $day - 1;

		$query1 = \DB::select('login_user_id')
		->from('logins')
		->join('users', 'INNER')
		->on('login_user_id', '=', 'user_id')

		->where('login_date', 'between', array(\DB::expr("curdate() - interval {$day} day"),  \DB::expr("curdate() - interval {$yday} day")))
		->and_where('attribute', 'general')
		->distinct(true);

		$dayx_login_user = $query1->execute()->as_array();

		if (empty($dayx_login_user)) {
			return $dayx_login_user = 0;
		}

		$query2 = \DB::select('login_user_id')
		->from('logins')
		->join('users', 'INNER')
		->on('login_user_id', '=', 'user_id')

		->where('login_date', 'between', array(\DB::expr('curdate() - interval 1 day'),  \DB::expr('curdate()')))
		->and_where('login_user_id', 'in', $dayx_login_user[0])
		->and_where('attribute', 'general')
		->distinct(true);

		return $query2->execute()->as_array();
	}

	private function setMessage($num)
	{
		$per_day2RR 	= ($num['day2RR'] === 0)	? 0 : $num['day2RR'] 	/ $num['dau'] * 100;
		$per_day3RR 	= ($num['day3RR'] === 0)	? 0 : $num['day3RR'] 	/ $num['dau'] * 100;
		$per_day5RR 	= ($num['day5RR'] === 0)	? 0 : $num['day5RR'] 	/ $num['dau'] * 100;
		$per_day7RR 	= ($num['day7RR'] === 0)	? 0 : $num['day7RR'] 	/ $num['dau'] * 100;
		$per_day14RR 	= ($num['day14RR'] === 0)	? 0 : $num['day14RR'] 	/ $num['dau'] * 100;

		$per_day2RR		= round($per_day2RR, 2);
		$per_day3RR		= round($per_day3RR, 2);
		$per_day5RR		= round($per_day5RR, 2);
		$per_day7RR		= round($per_day7RR, 2);
		$per_day14RR	= round($per_day14RR, 2);

		$this->message = ""
			."\n *Wake up and smell the coffee!*"
			."\n *DAU       : {$num['dau']}*"
			."\n *New User  : {$num['newUser']}*"
			."\n  gochi     : {$num['gochi']}"
			."\n  Post      : {$num['post']}"
			."\n  Comment   : {$num['comment']}"
			."\n"
			."\n *Day2-RR   : {$num['day2RR']} ({$per_day2RR}%)*"
			."\n  Day3-RR   : {$num['day3RR']} ({$per_day3RR}%)"
			."\n  Day5-RR   : {$num['day5RR']} ({$per_day5RR}%)"
			."\n *Day7-RR   : {$num['day7RR']} ({$per_day7RR}%)*"
			."\n *Day14-RR  : {$num['day14RR']} ({$per_day14RR}%)*"
		."";
	}

	private function sendEmail()
	{
		$email = \Email::forge();
		$email->from('onibi@fuel.php', 'Gocci-Test Server');
		$email->to('a-murata@inase-inc.jp', 'Akira Murata');
		$email->subject('Today\'s KPI');
		$email->body("$this->message");

		try {
			$email->send();
		}
		catch (\EmailValidationFailedException $e) {
			$err_msg = '送信に失敗しました。';
			error_log($err_msg);
		}
		catch (\EmailSendingFailedException $e) {
			$err_msg = '送信に失敗しました。';
			error_log($err_msg);
		}
	}

	private function sendSlack()
	{
		$message 	= $this->message;
		$color 		= 'danger';
		$channel    = '@channel';

		$data = array('attachments' => array(array(
			'text' 		=> $message,
			'color' 	=> $color,
			'mrkdwn_in' => array('text'),
		)));

		$json = json_encode(
            $data,
            JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|
            JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT
        );

		`curl -X POST --data-urlencode 'payload={$json}' https://hooks.slack.com/services/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`;
	}
}