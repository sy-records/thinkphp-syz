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

    /**
     * 通过邮箱获取Gravatar
     * 博客地址：https://qq52o.me/2239.html
     */
    public function getGravatarDemo()
    {
        $email = '52o@qq52o.cn';
        $imgUrl = getGravatar($email);
        echo "<img src=" . $imgUrl . " />";
    }

    /**
     * 发送消息到钉钉机器人
     * 文档地址: https://open-doc.dingtalk.com/docs/doc.htm?treeId=257&articleId=105735&docType=1
     * @param $title 标题
     * @param string $description 消息描述
     * @param string $link 内容链接，$messageType为link时，此参数不能为空
     * @param string $messageType 消息类型，link，markdown
     * @param string $robotUrl 所使用的机器人地址
     * @param string $data 完整内容
     * @return mixed
     */
    public function sendMessageToDingdingRobot($title, $description = '点击查看', $link = '', $messageType = 'link', $robotUrl = '', $data = '内容为空')
    {
        if ($messageType == 'link') {
            $json = json_encode([
                'msgtype' => 'link',
                'link' => [
                    'title' => $title,
                    "messageUrl" => $link,
                    "text" => $description
                ]
            ]);
        } elseif ($messageType == 'markdown') {
            // markdown 消息
        }

        $result = myCurl($robotUrl, $json , 1);
        return $result;
    }
}