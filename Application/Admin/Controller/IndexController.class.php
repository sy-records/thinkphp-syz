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

    /**
     * 使用webhook自动部署项目代码
     * @return int
     */
    public function gitWebHook()
    {
        $target = '/www/wwwroot/WordPress-tools'; // 生产环境web目录
        //密钥
        $secret = "test6666";
        //获取GitHub发送的内容
        $json = file_get_contents('php://input');
        $content = json_decode($json, true);
        //github发送过来的签名
        $signature = $_SERVER['HTTP_X_HUB_SIGNATURE'];
        if (!$signature) {
            return http_response_code(404);
        }
        list($algo, $hash) = explode('=', $signature, 2);
        //计算签名
        $payloadHash = hash_hmac($algo, $json, $secret);
        // 判断签名是否匹配
        if ($hash === $payloadHash) {
            $cmd = "cd $target && git pull";
            $res = shell_exec($cmd);
            $res_log = 'Success:'.PHP_EOL;
            $res_log .= $content['head_commit']['author']['name'] . ' 在' . date('Y-m-d H:i:s') . '向' . $content['repository']['name'] . '项目的' . $content['ref'] . '分支push了' . count($content['commits']) . '个commit：' . PHP_EOL;
            $res_log .= $res.PHP_EOL;
            $res_log .= '======================================================================='.PHP_EOL;
            echo $res_log;
        } else {
            $res_log  = 'Error:'.PHP_EOL;
            $res_log .= $content['head_commit']['author']['name'] . ' 在' . date('Y-m-d H:i:s') . '向' . $content['repository']['name'] . '项目的' . $content['ref'] . '分支push了' . count($content['commits']) . '>个commit：' . PHP_EOL;
            $res_log .= '密钥不正确不能pull'.PHP_EOL;
            $res_log .= '======================================================================='.PHP_EOL;
            echo $res_log;
        }
    }
}