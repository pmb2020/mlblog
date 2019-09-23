<?php
namespace app\admin\controller;
use think\Controller;
use think\Session;

class Login extends Controller {

	public function index() {
		if (request()->isPost()) {
			$data = input('post.');
			if ($data['username'] == 'wangbo' && $data['password'] == '123456') {
				Session::set('admin_name', $data['username']);
				$this->success('登录成功！', url('/admin/index'));
			} else {
				$this->error('账号或密码错误！', url(''));
			}
		}
		return view('/login');
	}
}