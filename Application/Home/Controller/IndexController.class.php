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
		qrcode($url, $size);
	}

	/**
	 * 封装一个P方法打印数组
	 * @return [type] [description]
	 */
	public function p()
    {
		$array = array(
			't0' => 'test0',
			't1' => 'test1',
			't2' => array(
				'tt0' => 'test0',
				'tt1' => 'test1',
			),
		);
		p($array);
	}
	
}
