<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
	'__pattern__' => [
		'name' => '\w+',
	],
	'[hello]' => [
		':id' => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
		':name' => ['index/hello', ['method' => 'post']],
	],

	// 前台路由
	'ajishu' => 'index/index/ajishu',
	'ashare' => 'index/index/ashare',
	'shareId/:id' => 'index/index/shareId',
	'alife' => 'index/index/alife',
	'apinbo' => 'index/index/apinbo',
	'gbook' => 'index/index/gbook',
	'about' => 'index/index/about',
	'info/:id' => 'index/index/info',
	'article/:id' => 'index/base/chongdx',

	// 后台路由
	// admin/login
	'admin/login' => 'admin/login/index',
	'admin/index' => 'admin/index/index',
	'admin/write' => 'admin/index/write',
	'admin/update/:id' => 'admin/index/update',
	'admin/list' => 'admin/index/list',
	'admin/webtj' => 'admin/index/webtj',
	// 'admin/webtjType/:id' => 'admin/index/webtjType',
	'admin/webset' => 'admin/index/webset',
	'admin/comment' => 'admin/index/comment',
	'admin/test/:id' => 'admin/index/test',
	'admin/del/:id' => 'admin/index/del',
	'admin/loginOut' => 'admin/index/loginOut',
	// 'admin/del/:id' => 'admin/index/del',

];
