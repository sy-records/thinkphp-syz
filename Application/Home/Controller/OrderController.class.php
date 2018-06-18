<?php
namespace Home\Controller;
use Think\Controller;
use Vendor\Juhe\Exp;
use Vendor\Weixin\WeChatTemplete;

class OrderController extends Controller
{
    /**
     * 微信模板消息发送接口示例demo
     */
    public function sendWeChatTemplete()
    {
        vendor('Weixin.WeChatTemplete');
        $openid = ''; //用户的openid
        $templateId = C('TEMPLETEID.5'); //微信模板ID
//        详细内容
//        {{first.DATA}}
//        手机号：{{keyword1.DATA}}
//        时间：{{keyword2.DATA}}
//        {{remark.DATA}}
//        内容示例
//        您好，欢迎注册沈唁志！
//        手机号：13800000000
//        时间：2016-05-03 12:00:00
//        沈唁博客(qq52o.me)是关注PHP开发等技术的个人博客，同时是个人程序人生的点滴记录和时光储备。
//        （点击跳转到首页）
        $data= array(
            'first'=> array('value'=>'您好，欢迎注册沈唁志！'),//推荐人昵称
            'keyword1'=> array('value'=>'13800000000'),  //手机号
            'keyword2'=> array('value'=>date("Y-m-d H:i:s",time())), //时间 格式 '2016-05-03 12:00:00'
            'remark'=> array('value'=>'沈唁博客(qq52o.me)是关注PHP开发等技术的个人博客，同时是个人程序人生的点滴记录和时光储备。'),
        );
        $url = 'https://qq52o.me/'; //点击模板详情跳转地址 默认为null

        $appid = C('WECHAT.appid');
        $key = C('WECHAT.appKey');
        $wct = new WeChatTemplete($appid,$key); //初始化类
        $result = $wct->sendTemplate($openid, $templateId, $data, $url); //执行发送
        p($result);
    }
}