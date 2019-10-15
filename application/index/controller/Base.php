<?php
namespace app\index\controller;
use think\Controller;
use think\Cookie;

/**
 * index基类
 */
class Base extends Controller {

	public function _initialize() {
		if (!Cookie::has('ml_count')) {
			$count_arr = [
				'ip' => $this->getIP11(),
				'url' => $this->getUrl(),
				'device' => $this->isMobile(),
				'referer' => $this->getFromPage(),
			];
			$count_arr['last_time'] = $this->lastTime($count_arr['ip']);
			//同一ip一小时不重复记录
			if ($count_arr['last_time'] + 3600 < time()) {
				db('ml_count')->insert($count_arr);
			}
			Cookie::set('ml_count', db('ml_count')->max('id'), 3600);
		} else {
			db('ml_count')->where('id', Cookie::get('ml_count'))->setInc('page_num');
		}

	}
	//获取访客ip(暂不使用)
	function getIP11() {
		if (getenv('HTTP_CLIENT_IP')) {
			$ip = getenv('HTTP_CLIENT_IP');
		} elseif (getenv('HTTP_X_FORWARDED_FOR')) {
			$ip = getenv('HTTP_X_FORWARDED_FOR');
		} elseif (getenv('HTTP_X_FORWARDED')) {
			$ip = getenv('HTTP_X_FORWARDED');
		} elseif (getenv('HTTP_FORWARDED_FOR')) {
			$ip = getenv('HTTP_FORWARDED_FOR');

		} elseif (getenv('HTTP_FORWARDED')) {
			$ip = getenv('HTTP_FORWARDED');
		} else {
			$ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
		}

		return $ip;
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
			for ($i = 0; $i < ml_count($ips); $i++) {
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
		$res = db('ml_count')->field('time')->where('ip', $ip)->order('id desc')->find();
		if ($res) {
			return strtotime($res['time']);
		}
		return 0;
	}

	//根据UA判断PC还是移动
	public function isMobile() {
		$ua = $_SERVER['HTTP_USER_AGENT'];
		if (stripos($ua, 'Windows')) {
			return 'PC';
		}
		if (stripos($ua, 'Android')) {
			return '移动';
		}
		if (stripos($ua, 'Baiduspider')) {
			return '百度';
		}
		if (stripos($ua, 'Googlebot')) {
			return '谷歌';
		}
		if (stripos($ua, 'bingbot')) {
			return '必应';
		}
		if (stripos($ua, 'spider')) {
			if (stripos($ua, 'sogou')) {
				return '搜狗';
			}
			if (stripos($ua, '360')) {
				return '360';
			}
			return '其他';
		}
		return '未知';
	}

	public function chongdx($id) {
		Header("HTTP/1.1 301 Moved Permanently");
		$arr = [
			'1540478858' => 1,
			'1540710791' => 4,
			'1540711196' => 5,
			'1540780846' => 2,
			'1540782108' => 3,
			'1544089250' => 22,
			'1544623071' => 24,
			'1541905140' => 15,
			'1542276845' => 17,
			'1544351000' => 23,
		];
		if (empty($arr[$id])) {
			Header("Location: http://www.gold404.cn");
		} else {
			$url = 'Location: http://www.gold404.cn/info/' . $arr[$id] . '.html';
			Header($url);
		}
	}

}