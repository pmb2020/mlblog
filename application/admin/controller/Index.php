<?php
namespace app\admin\controller;
use think\Session;

/**
 * admin模块
 */
class Index extends Base {

	public function index() {
		// dump(Session::get('admin_name'));
		return view('/index');
	}

	public function write() {
		Session::set('max_id', db('ml_article')->max('id'));
		if (request()->isPost()) {
			$data = input('post.');
			// dump($data);die();
			$res = db('ml_article')->insert($data);
			if ($res == 1) {
				$this->success('添加成功！', url('/admin/list'), '', 1);
			} else {
				$this->success('添加失败！', url('/admin/list'), '', 1);
			}
		}

		return view('/write');
	}
	public function list() {
		$result = db('ml_article')->field('id,type,title,time,read_num,comment_num')->order('id desc')->paginate(10);
		$page = $result->render();
		$result = $result->all();
		foreach ($result as $key => &$value) {
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
		}
		$this->assign('page', $page);
		$this->assign('list', $result);
		return view('/list');
	}
	// 删除文章
	public function del($id) {
		if (request()->isGet()) {
			$res = db('ml_article')->delete($id);

			if ($res == 1) {
				$this->success('删除成功！', url('/admin/list'), '', 1);
			} else {
				$this->success('删除失败！', url('/admin/list'), '', 1);
			}
		}
	}
	// 网站统计
	public function webtj() {
		// phpinfo();
		// die();
		return view('/webtj');
	}
	// 注销登录
	public function loginOut() {
		Session::clear();
		$this->success('退出成功！', url('/admin/login'), '', 1);
	}
	// 上传图片
	public function upload() {
		$file = request()->file('file');
		if ($file) {
			$path = ROOT_PATH . 'public' . DS . 'static' . DS . 'titleimg';
			$info = $file->validate(['size' => 5 * 1024 * 1024, 'ext' => 'jpg,png,gif'])->rule('rename1')->move($path);
			if ($info) {
				return '图片上传成功';
			} else {return $file->getError();}
		} else {
			return '没有接收到图片';
		}

	}
	//编辑器上传图片
	public function uploadImg() {
		$file = request()->file('file');

		$info = $file->validate(['size' => 5 * 1024 * 1024, 'ext' => 'jpg,png,gif'])->rule('myName')->move(ROOT_PATH . 'public' . DS . 'uploads');
		if ($info) {
			$name_path = str_replace('\\', "/", $info->getSaveName());
			$res = [
				'errno' => 0,
				'data' => ['/uploads/' . $name_path],
			];
		} else {
			$res = [
				'errno' => 1,
				'data' => [$file->getError()],
			];
		}
		return json_encode($res);
	}

}