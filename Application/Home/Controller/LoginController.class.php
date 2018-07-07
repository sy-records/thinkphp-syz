<?php
namespace Home\Controller;

use Think\Controller;

class LoginController extends Controller
{
    public function _initialize(){
        // 验证用户是否登录
        $userId = S('userId');
        if(!$userId){
            //获取当前网页，授权后返回
            $callBackUrl = urlencode(__SELF__);

            // 微信用户
            $wechat = C('WECHAT');
            $appid = $wechat['appid'];
            $redirect_uri = urlencode ('http://'.$_SERVER['HTTP_HOST'].'/Login/authCallback?mid='.S('mid').'&CallbackUrl='.$callBackUrl);
            $url ="https://open.weixin.qq.com/connect/oauth2/authorize?appid={$appid}&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_userinfo&state=1&connect_redirect=1#wechat_redirect";
            header("Location:".$url);
            exit(0);
        }
    }

    public function authCallback($code)
    {
        $wechat = C('WECHAT');
        $appid = $wechat['appid'];
        $secret = $wechat['appKey'];

        // 获取用户授权信息
        $authInfo = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$secret}&code={$code}&grant_type=authorization_code";
        $authInfo = $this->getJson($authInfo);

        //第二步:根据全局access_token和openid查询用户信息
        $access_token = $authInfo["access_token"];
        $openid = $authInfo['openid'];
        if(!$openid){
            $this->error('授权失败，稍后请重试!');
        }

        // 根据用户openid查看用户是否存在
        $user = M('User')->where("wx_openid = '%s '",$openid);

        // 存在则直接登录
        if($user){
            $callBackUrl = urldecode(I('get.CallbackUrl'));
            S('userId', $user['id']);
            header("Location:".'http://'.$_SERVER['HTTP_HOST'].$callBackUrl);
            exit(0);
        }

        // 存储openid 进行注册
        S('wx_openid', $openid);
        // ...省略

        // 获取用户微信信息
        $getWxInfourl = "https://api.weixin.qq.com/sns/userinfo?access_token={$access_token}&openid={$openid}&lang=zh_CN";
        $wxUserInfo = $this->getJson($getWxInfourl);
        S('userInfo', $wxUserInfo);
        header('Location:'.U('/login/login'));
        exit(0);
    }

    protected function getJson($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);

        return json_decode($output, true);
    }
}
