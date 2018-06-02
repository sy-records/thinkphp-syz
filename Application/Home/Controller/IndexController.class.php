<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {

    /**
     * $url 链接
     * $size 大小
     */
    public function qrcode()
    {
        $url = "https://qq52o.me/";
        $size = '6';
        qrcode($url,$size);
    }
}