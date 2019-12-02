<?php
namespace app\index\controller;
// use think\Controller;
use think\Db;
use think\Session;

class Index extends Base {
	public function index() {
		$result = db('ml_article')->order('is_top desc,id desc')->paginate(10);
		$page = $result->render();
		$result = changeType($result->all());
		$this->assign([
			'hot_data' => hotData(),
			'page' => $page,
			'list' => $result,
		]);
		return view('/index');
	}

	public function ajishu() {
		$result = db('ml_article')->where('type', 1)->order('is_top desc,id desc')->paginate(10);
		$page = $result->render();
		$result = changeType($result->all());
		$this->assign([
			'hot_data' => hotData(),
			'page' => $page,
			'list' => $result,
		]);
		return view('/ajishu');
	}

	public function alife() {
		$result = db('ml_article')->where('type', 0)->order('is_top desc,id desc')->paginate(10);
		$page = $result->render();
		$result = changeType($result->all());
		$this->assign([
			'hot_data' => hotData(),
			'page' => $page,
			'list' => $result,
		]);
		return view('/alife');
	}
	public function ashare() {
		$result = db('ml_article')->where('type', '>', 3)->order('is_top desc,id desc')->paginate(10);
		$page = $result->render();
		$result = changeType($result->all());
		$this->assign('page', $page);
		$this->assign('list', $result);
		return view('/ashare');
	}
	public function shareId($id) {
		$result = db('ml_article')->where('status', 0)->where('type', $id + 3)->order('is_top desc,id desc')->paginate(10);
		$page = $result->render();
		$result = changeType($result->all());
		$this->assign('page', $page);
		$this->assign('list', $result);
		return view('/ashare');
	}
	public function info($id) {
		//阅读量+1
		Db::table('ml_article')->where('id', $id)->setInc('read_num');
		$res1 = db('ml_article')->where('id', $id)->find();
		$data1 = db('ml_article')->where("id", ">", $res1['id'])->field('id,title')->order("id", "asc")->find();
		$data2 = db('ml_article')->where("id", "<", $res1['id'])->field('id,title')->order("id", "desc")->find();
		$data3 = db('ml_article')->field('id,title')->limit(6)->order('rand()')->select(); //随机推荐
		if (!$data2) {
			$data2['id'] = '0';
			$data2['title'] = '没有上一篇了';}
		if (!$data1) {
			$data1['id'] = '0';
			$data1['title'] = '没有下一篇了';}
		// dump(type($res1));die();
		$res1['desc'] = mb_substr(strip_tags($res1['content']), 0, 150, 'utf-8'); //seo描述
		//判断文章是否加密
		if ($res1['status'] == 1) {
			if (!Session::has('admin_name')) {
				$res1['content'] = cutTab($res1['content'], 150, '......') . '<p class="jiami_p">该文章仅作者可见</p>';
			}
		}
		$this->assign([
			'hot_data' => hotData(),
			'data' => type($res1),
			'next_title' => $data1,
			'top_title' => $data2,
			'rand_title' => $data3,
		]);
		return view('/info');
	}
	public function apinbo() {
		return view('/apinbo');
	}
	public function gbook() {
		return view('/gbook');
	}
	public function about() {
		$this->assign('hot_data', hotData());
		return view('/about');
	}
}
//热门文章数据
function hotData() {
	$hot_data = Db::query("select id,type,title,read_num,time from ml_article order by read_num desc limit 5");
	$hot_data = changeType($hot_data);
	foreach ($hot_data as $key => &$value) {
		$value['time'] = substr($value['time'], 0, 10);
	}
	return $hot_data;
}
function type($arr) {
	switch ($arr['type']) {
	case '0':
		$arr['type'] = '爱生活';
		$arr['type_link']='alife';
		break;
	case '1':
		$arr['type'] = '爱技术';
		$arr['type_link']='ajishu';
		break;
	case '3':
		$arr['type'] = '爱拼搏';
		$arr['type_link']='apinbo';
		break;
	default:
		$arr['type'] = '爱分享';
		$arr['type_link']='ashare';
		break;
	}
	return $arr;
}
//无损截取包括html标签的字符串
function cutTab($string, $length = '15', $dot = '…') {
	$_lenth = mb_strlen($string, "utf-8");
	$text_str = preg_replace("/<img.*?>/si", "", $string);
	$text_lenth = mb_strlen($text_str, "utf-8") - 1;

	if ($text_lenth <= $length) {
		return stripcslashes($string);
	}

	if (strpos($string, '<img') === false) {
		$res = mb_substr($string, 0, $length, 'UTF-8');
		return stripcslashes($res) . $dot;
	}

	//计算标签位置
	$html_start = ceil(strpos($string, '<img') / 3);
	$html_end = ceil(strpos($string, '/>') / 3);

	if ($length < $html_start) {
		$res = mb_substr($string, 0, $length, 'UTF-8');
		return stripcslashes($res) . $dot;
	}

	if ($length > $html_start) {

		$res_html = mb_substr($text_str, 0, $length - 1, 'UTF-8');

		preg_match('/<img[^>]*\>/', $string, $result_html);
		$before = mb_substr($res_html, 0, $html_start, 'UTF-8');
		$after = mb_substr($res_html, $html_start, mb_strlen($res_html, "utf-8"), 'UTF-8');
		$res = $before . $result_html[0] . $after;
		return stripcslashes($res) . $dot;
	}

}

// 类型输出转换
function changeType($result) {
	$path = $_SERVER['DOCUMENT_ROOT'] . '/static/titleimg/';
	if (!file_exists($path)) {
		mkdir($path);
	}
	$img_arr = scandir($path);
	foreach ($img_arr as $key => $value) {
		$img_arr[$key] = explode('.', $value)[0];
	}
	foreach ($result as $key => &$value) {
		if (!empty($value['content'])) {
			$value['content'] = mb_substr(strip_tags($value['content']), 0, 180, 'utf-8');
		}
		switch ($value['type']) {
		case '0':
			$value['type'] = '爱生活';
			break;
		case '1':
			$value['type'] = '爱技术';
			break;
		case '3':
			$value['type'] = '爱拼搏';
			break;
		default:
			$value['type'] = '爱分享';
			break;
		}
		if (in_array($value['id'], $img_arr)) {
			$result[$key]['titleimg'] = 1;
		} else { $value['titleimg'] = 0;}
	}
	return $result;
}
