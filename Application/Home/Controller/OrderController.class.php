<?php
namespace Home\Controller;
use Think\Controller;
use Vendor\Juhe\Exp;

class OrderController extends Controller
{
    /**
     * 聚合快递查询接口
     */
    public function exp()
    {
        Vendor('Juhe.Exp');
        header('Content-type:text/html;charset=utf-8');
        $params = array(
            'key' => '1dd8d968cc60823d6f44fe8f6e9e8dac', //申请的快递appkey
            'com' => 'sto', //快递公司编码，可以通过$exp->getComs()获取支持的公司列表
            'no'  => '336330266666' //快递编号
        );
        $exp = new Exp($params['key']); //初始化类

        $result = $exp->query($params['com'],$params['no']); //执行查询

        if($result['error_code'] == 0){//查询成功
            $list = $result['result']['list'];
            dump($list);
        }else{
            echo "获取失败，原因：".$result['reason'];
        }

    }
}