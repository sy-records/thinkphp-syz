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
    'WECHAT' => [
        'appid' => '',
        'appKey' => '',
        'apiKey' => '',
        'mchid' => '',
        'certificate_path' => dirname(dirname(__FILE__)).'/Credential',
    ],
    // 配置邮件发送服务器
    'MAIL_SMTP'                     =>TRUE,
    'MAIL_HOST'                     =>'smtp.exmail.qq.com',//邮件发送SMTP服务器
    'MAIL_SMTPAUTH'                 =>TRUE,
    'MAIL_USERNAME'                 =>'52o@qq52o.cn',//SMTP服务器登陆用户名
    'MAIL_PASSWORD'                 =>'XXXXXXXXXXXXXXXXXXXX',//SMTP服务器登陆密码
    'MAIL_SECURE'                   =>'tls',
    'MAIL_CHARSET'                  =>'utf-8',
    'MAIL_ISHTML'                   =>TRUE,
);