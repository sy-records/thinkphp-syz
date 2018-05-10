<?php
namespace Admin\Controller;

use Think\Controller;

class OrderController extends Controller {

    public function index(){
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
    
}