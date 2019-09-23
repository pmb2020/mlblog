<?php
namespace app\admin\controller;
use think\Controller;
use think\Session;

/**
 * admin基类
 */
class Base extends Controller {

	public function _initialize() {
		if (!Session::has('admin_name')) {
			$this->error('请您先登录！', url('/admin/login'));
		}

	}
}