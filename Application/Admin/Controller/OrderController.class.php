<?php
namespace Admin\Controller;
use Think\Controller;
class OrderController extends Controller {

    /**
     * PHPExcel导出功能
     */
    public function excelDemo(){
        $header = array(
            array(NULL, 沈唁, 集成, Excel)
        );
        $orderinfo = array(
            array(1,2,3,4),
            array(5,6,7,8),
            array(1,3,5,7)
        );
        $data = array_merge($header,$orderinfo);
        createXls($data);
    }

    
    /**
     * 导出csv格式的Excel
     */
    public function csvDemo()
    {
        $data = array(
            '1,2,3,4,5',
            '6,7,8,9,0',
            '1,3,5,7,9'
        );
        $header='ID,用户名,密码,性别,手机号';
        createCsv($data,$header);
    }
}