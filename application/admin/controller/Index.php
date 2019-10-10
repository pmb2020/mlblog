<?php
namespace app\admin\controller;
use app\admin\model\MlCount;
use think\Model;
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
		if (request()->isPost()) {
			$data = input('post.');
			if ($data['status'] == 2) {
				unset($data['status']);
				$data['is_top'] = 1;
			}
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
		$type = request()->param('type');
		// 0默认 1加密
		// $map = '';
		$map = 'status > -1';
		if ($type == 'private') {
			$map = 'status = 1';
		}if ($type == 'public') {
			$map = 'status = 0';
		}if ($type == 'top') {
			$map = 'is_top <> 0';
		}
		// dump($map);
		$result = db('ml_article')->where($map)->field('id,type,title,time,read_num,comment_num')->order('id desc')->paginate(10);
		$page = $result->render();
		$result = $result->all();
		$num = [
			'all' => db('ml_article')->count(),
			'top' => db('ml_article')->where('is_top <> 0')->count(),
			'public' => db('ml_article')->where('status = 0')->count(),
			'private' => db('ml_article')->where('status = 2')->count(),
		];
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
		$this->assign([
			'list' => $result,
			'page' => $page,
			'num' => $num,
		]);
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
		$type = request()->param('type');
		if ($type != 'pc' && $type != 'mobile') {
			$map = '';
		} else {
			if ($type == 'mobile') {
				$type = '移动';
			}
			$map['device'] = $type;
		}

		$rel = db('ml_count')->where($map)->order('id desc')->paginate(20);
		$num = [
			'all' => db('ml_count')->count(),
			'pc' => db('ml_count')->where('device', 'pc')->count(),
			'mobile' => db('ml_count')->where('device', '移动')->count(),
		];
		$page = $rel->render();
		$rel = $this->chageTime($rel->all());
		$this->assign([
			'list' => $rel,
			'page' => $page,
			'num' => $num,
		]);
		return view('/webtj');
	}
	public function webtjType($id) {
		return $id;
	}
	public function comment() {
		return view('/comment');
	}

	public function webset() {
		return view('/webset');
	}
	// 注销登录
	public function loginOut() {
		Session::clear();
		$this->success('退出成功！', url('/admin/login'), '', 1);
	}

	//转换访客记录中的上次访问时间
	function chageTime($rel) {
		foreach ($rel as $key => &$value) {
			if ($value['last_time'] != 0) {
				$value['last_time'] = intval((strtotime($value['time']) - $value['last_time']) / 3600);
				if ($value['last_time'] >= 24) {
					$value['last_time'] = intval($value['last_time'] / 24) . '天前';
				} else {
					$value['last_time'] = $value['last_time'] . '小时前';
				}
			}
		}
		return $rel;
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