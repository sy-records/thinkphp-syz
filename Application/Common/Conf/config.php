<?php
return array(
	//'配置项'=>'配置值'
	'URL_MODEL'=>2,//设置URL模式重写模式
	'DEFAULT_MODULE'=>'Home',//设置默认模块
	'MODULE_ALLOW_LIST'=>array('Home','Admin'),//设置允许访问的模块
	//增加自定义的模板替换配置信息
	'TMPL_PARSE_STRING'=>array(
		'__PUBLIC_ADMIN__'=>'/Public/Admin',
		'__PUBLIC_HOME__'=>'/Public/Home',
		),
	'SHOW_PAGE_TRACE'=>true,
    // 引入其他配置信息
	'LOAD_EXT_CONFIG' => 'wechat,db,email',
	//聚合数据快递查询 APPKEY
	'EXPRESS_APP_KEY' =>'0b3f2389b9bb77cf8f3c6c163b278617',
);