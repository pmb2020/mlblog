<?php
namespace app\index\controller;
use think\Controller;
use think\Cookie;

/**
 * index基类
 */
class Base extends Controller {

	public function _initialize() {
		if (!Cookie::has('count')) {
			$count_arr = [
				'ip' => $this->getIp(),
				'url' => $this->getUrl(),
				'device' => $this->isMobile(),
				'referer' => $this->getFromPage(),
			];
			$count_arr['last_time'] = $this->lastTime($count_arr['ip']);
			db('count')->insert($count_arr);
			Cookie::set('count', db('count')->max('id'), 3 * 3600);
			// echo "string" . Cookie::get('count');
		}
		db('count')->where('id', Cookie::get('count'))->setInc('page_num');

	}

	//获取访客ip
	public function getIp() {
		$ip = false;
		if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
			$ip = $_SERVER["HTTP_CLIENT_IP"];
		}
		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ips = explode(", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
			if ($ip) {
				array_unshift($ips, $ip);
				$ip = FALSE;}
			for ($i = 0; $i < count($ips); $i++) {
				if (!eregi("^(10│172.16│192.168).", $ips[$i])) {
					$ip = $ips[$i];
					break;
				}
			}
		}
		return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
	}

	//获取网站来源
	public function getFromPage() {
		if (!isset($_SERVER['HTTP_REFERER'])) {
			return '-'; //直接访问
		}
		return $_SERVER['HTTP_REFERER'];
	}
	// 获取当前页面url
	public function getUrl() {
		if (!isset($_SERVER['REDIRECT_PATH_INFO'])) {
			return '-'; //直接访问
		}
		return $_SERVER['REDIRECT_PATH_INFO'];
	}
	//上次访问时间
	public function lastTime($ip) {
		$res = db('count')->field('time')->where('ip', $ip)->order('id desc')->find();
		if ($res) {
			return strtotime($res['time']);
		}
		return time();
	}
	//根据UA判断PC还是移动
	function isMobile() {
		$useragent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
		$useragent_commentsblock = preg_match('|\(.*?\)|', $useragent, $matches) > 0 ? $matches[0] : '';
		function CheckSubstrs($substrs, $text) {
			foreach ($substrs as $substr) {
				if (false !== strpos($text, $substr)) {
					return 1;
				}
			}

			return 2;
		}
		$mobile_os_list = array('Google Wireless Transcoder', 'Windows CE', 'WindowsCE', 'Symbian', 'Android', 'armv6l', 'armv5', 'Mobile', 'CentOS', 'mowser', 'AvantGo', 'Opera Mobi', 'J2ME/MIDP', 'Smartphone', 'Go.Web', 'Palm', 'iPAQ');
		$mobile_token_list = array('Profile/MIDP', 'Configuration/CLDC-', '160×160', '176×220', '240×240', '240×320', '320×240', 'UP.Browser', 'UP.Link', 'SymbianOS', 'PalmOS', 'PocketPC', 'SonyEricsson', 'Nokia', 'BlackBerry', 'Vodafone', 'BenQ', 'Novarra-Vision', 'Iris', 'NetFront', 'HTC_', 'Xda_', 'SAMSUNG-SGH', 'Wapaka', 'DoCoMo', 'iPhone', 'iPod');

		$found_mobile = CheckSubstrs($mobile_os_list, $useragent_commentsblock) ||
		CheckSubstrs($mobile_token_list, $useragent);

		if ($found_mobile) {
			return 1;
		} else {
			return 2;
		}
	}

}