<?php
namespace app\index\controller;
use think\Controller;
use think\Request;

/**
 * 评论控制器
 */
class Comment extends Controller {

	function index() {
		$data = input('post.');
		// $data = json_decode($data);
		// unset($data['email']);
		$res = db('ml_comment')->insert($data);
		// return gettype($data);
		// $asad = ['titlt', 'wwwwwwww'];\
		if ($res==1) {
			$response=[
				'code'=>200,
				'msg' =>'成功'
			];
		}else{
			$response=[
				'code'=>500,
				'msg' =>'失败'
			];
		}
		return json_encode($response);
	}

	public function commentAll()
	{
		$com=db('ml_comment')->where('p_id',0)->select();
		return json_encode($com,JSON_UNESCAPED_UNICODE);
	}
}