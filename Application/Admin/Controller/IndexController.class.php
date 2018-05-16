<?php
namespace Admin\Controller;
use Think\Controller;

class IndexController extends Controller {
    /**
     * 发送邮件demo
     * @throws \phpmailerException
     */
    public function sendMail()
    {
        $to = '52o@qq52o.cn';
        $subject = '邮件标题';
        $content = '邮件内容';
        if(sendMail($to,$subject,$content)){
            $this->success('发送成功');
        }else{
            $this->error('发送失败');
        }
    }
}