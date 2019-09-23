<?php
namespace app\index\controller;
use think\Controller;
use think\Db;

class Index extends Controller {
	public function index() {
		$result = db('ml_article')->where('status', 0)->field('id,type,title,content,time,read_num,comment_num')->order('id desc')->paginate(10);
		$page = $result->render();
		$result = changeType($result->all());
		$this->assign('page', $page);
		$this->assign('list', $result);
		return view('/index');
	}

	public function ajishu() {
		$result = db('ml_article')->field('id,type,title,content,time,read_num,comment_num')->order('id desc')->paginate(10);
		$page = $result->render();
		$result = changeType($result->all());
		$this->assign('page', $page);
		$this->assign('list', $result);
		return view('/ajishu');
	}

	public function alife() {
		$result = db('ml_article')->field('id,type,title,content,time,read_num,comment_num')->order('id desc')->paginate(10);
		$page = $result->render();
		$result = changeType($result->all());
		$this->assign('page', $page);
		$this->assign('list', $result);
		return view('/alife');
	}
	public function ashare() {
		$result = db('ml_article')->where('status', 0)->where('type', '>', 3)->field('id,type,title,content,time,read_num,comment_num')->order('id desc')->paginate(10);
		$page = $result->render();
		$result = changeType($result->all());
		$this->assign('page', $page);
		$this->assign('list', $result);
		return view('/ashare');
	}
	public function info($id) {
		Db::table('ml_article')->where('id', $id)->setInc('read_num');
		$res1 = db('ml_article')->where('id', $id)->find();
		$data1 = db('ml_article')->where("id", ">", $res1['id'])->field('id,title')->order("id", "asc")->find();
		$data2 = db('ml_article')->where("id", "<", $res1['id'])->field('id,title')->order("id", "desc")->find();
		$data3 = db('ml_article')->field('id,title')->limit(6)->order('rand()')->select(); //随机推荐
		if (!$data2) {
			$data2['id'] = '';
			$data2['title'] = '没有上一篇了';}
		if (!$data1) {
			$data1['id'] = '';
			$data1['title'] = '没有下一篇了';}
		// dump(type($res1));die();
		$this->assign([
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
		return view('/about');
	}
}

function type($arr) {
	switch ($arr['type']) {
	case '0':
		$arr['type'] = '爱生活';
		break;
	case '1':
		$arr['type'] = '爱技术';
		break;
	case '3':
		$arr['type'] = '爱拼搏';
		break;
	default:
		$arr['type'] = '爱分享';
		break;
	}
	return $arr;
}

// 类型输出转换
function changeType($result) {
	$path = $_SERVER['DOCUMENT_ROOT'] . '/static/titleimg/';
	$img_arr = scandir($path);
	foreach ($img_arr as $key => $value) {
		$img_arr[$key] = explode('.', $value)[0];
	}
	foreach ($result as $key => &$value) {
		$value['content'] = substr(strip_tags($value['content']), 0, 310);
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
