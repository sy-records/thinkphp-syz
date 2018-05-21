<?php
return array(
    // 配置邮件发送服务器
    'MAIL_SMTP'                     =>TRUE,
    'MAIL_HOST'                     =>'smtp.exmail.qq.com',//邮件发送SMTP服务器
    'MAIL_SMTPAUTH'                 =>TRUE,
    'MAIL_USERNAME'                 =>'52o@qq52o.cn',//SMTP服务器登陆用户名
    'MAIL_PASSWORD'                 =>'XXXXXXXXXXXXXXXXXXXX',//SMTP服务器登陆密码
    'MAIL_SECURE'                   =>'ssl', //tls 端口25 ssl 465  //linux服务器的问题 用465
    'MAIL_PORT'                     =>'465', //tls 端口25 ssl 465
    'MAIL_CHARSET'                  =>'utf-8',
    'MAIL_ISHTML'                   =>TRUE,
);