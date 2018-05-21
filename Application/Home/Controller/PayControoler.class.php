<?php
namespace Home\Controller;
use Think\Controller;
class PayController extends Controller {

    public function index()
    {
        $this->display();
    }

    /**
     * 微信公众号支付
     */
    public function pay()
    {
        //部分代码逻辑省略
        vendor('Weixin.WeChatPay');
        $config = C('WECHAT');
        $model = new WeChatPay($config['mchId'],$config['appId'],$config['apiKey']);
        $result = $model->createJsBizPackage($user['wx_openid'],$order['money'],$order['order_sn'],'syz',U('pay/notify',[],[],true),time());
        $this->ajaxReturn(['code' => 200,'result' => $result]);
    }

    /**
     * 微信支付回调
     */
    public function notify()
    {
        $postStr = file_get_contents("php://input");
        $post = json_decode(json_encode(simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        if($post['result_code'] == 'SUCCESS' && $post['return_code'] == 'SUCCESS') {
            // 支付成功扭转订单状态
            $order_sn = $post['out_trade_no'];
            //$order_sn = 'SY201805121145102321380644';

            $model = new OrderRelationModel();
            $order = $model->where(['order_sn' => $order_sn,'status' => 1])->relation(true)->find();

            //完成你的业务逻辑 修改状态之类的

            //给微信返回 防止重复通知
            return '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
        }else{
            return ['msg' => '支付失败'];
        }
    }
}