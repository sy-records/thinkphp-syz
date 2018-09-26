<?php
/**
 * 数组转xls格式的excel文件
 * @param  array  $data      需要生成excel文件的数组
 * @param  string $filename  生成的excel文件名
 *      示例数据：
                $data = array(
                array(NULL, 2010, 2011, 2012),
                array('Q1',   12,   15,   21),
                array('Q2',   56,   73,   86),
                array('Q3',   52,   61,   69),
                array('Q4',   30,   32,    0),
                );
 */
function createXls($data, $filename = 'simple.xls')
{
	ini_set('max_execution_time', '0');
	Vendor('PHPExcel.PHPExcel');

	$filename = str_replace('.xls', '', $filename) . '.xls';

	$phpexcel = new PHPExcel();
	$phpexcel->getProperties()
//      右键属性所显示的信息
		->setCreator("By ShenYan") //作者
		->setLastModifiedBy("By ShenYan") //最后一次保存者
		->setTitle("excel") //标题
		->setSubject("excel") //主题
		->setDescription("excel") //描述
		->setKeywords("excel php") //标记
		->setCategory("result file"); //类别
	$phpexcel->getActiveSheet()->fromArray($data);
	$phpexcel->getActiveSheet()->setTitle('Sheet1');
	$phpexcel->setActiveSheetIndex(0);

	header('Content-Type: application/vnd.ms-excel');
	header("Content-Disposition: attachment;filename=$filename");
	header('Cache-Control: max-age=0');
	header('Cache-Control: max-age=1');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
	header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
	header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
	header('Pragma: public'); // HTTP/1.0

	$objwriter = PHPExcel_IOFactory::createWriter($phpexcel, 'Excel5');
	$objwriter->save('php://output');

	exit;
}

/**
 * 数据转csv格式的excel
 * @param  array $data      需要转的数组
 * @param  string $header   要生成的excel表头
 * @param  string $filename 生成的excel文件名
 *      示例数组：
                $data = array(
                '1,2,3,4,5',
                '6,7,8,9,0',
                '1,3,5,7,9'
                );
                $header='用户名,密码,头像,性别,手机号';
 */
function createCsv($data, $header = null, $filename = 'orderlist.csv')
{
	// 如果手动设置表头；则放在第一行
	if (!is_null($header)) {
		array_unshift($data, $header);
	}
	// 防止没有添加文件后缀
	$filename = str_replace('.csv', '', $filename) . '.csv';
	ob_clean();
	Header("Content-type:  application/octet-stream ");
	Header("Accept-Ranges:  bytes ");
	Header("Content-Disposition:  attachment;  filename=" . $filename);
	foreach ($data as $k => $v) {
		// 如果是二维数组；转成一维
		if (is_array($v)) {
			$v = implode(',', $v);
		}
		// 替换掉换行
		$v = preg_replace('/\s*/', '', $v);
		// 解决导出的数字会显示成科学计数法的问题
		$v = str_replace(',', "\t,", $v);
		// 转成gbk以兼容office乱码的问题
		echo iconv('UTF-8', 'GBK', $v) . "\t\r\n";
	}
}

/**
 * 发送邮件
 * @param $to
 * @param $subject
 * @param $content
 * @return bool
 * @throws phpmailerException
 */
function sendMail($to, $subject, $content)
{
	vendor('PHPMailer.class#phpmailer');
	$mail = new \PHPMailer(); //实例化
	// 装配邮件服务器
	if (C('MAIL_SMTP')) {
		$mail->IsSMTP(); //启动SMTP
	}
	$mail->Host = C('MAIL_HOST'); //SMTP服务器地址
	$mail->Port = C('MAIL_PORT'); //邮件端口
	$mail->SMTPAuth = C('MAIL_SMTPAUTH'); //启用SMTP认证
	$mail->Username = C('MAIL_USERNAME'); //邮箱名称
	$mail->Password = C('MAIL_PASSWORD'); //邮箱密码
	$mail->SMTPSecure = C('MAIL_SECURE'); //发件人地址
	$mail->CharSet = C('MAIL_CHARSET'); //邮件头部信息
	$mail->From = C('MAIL_USERNAME'); //SMTP服务器登陆用户名
	$mail->AddAddress($to);
	$mail->FromName = '十年之约项目组'; //发件人是谁
	//    $mail->AddAttachment('./Public/test.png','沈唁志.png'); // 添加附件,并指定名称
	$mail->IsHTML(C('MAIL_ISHTML')); //是否是HTML字样
	$mail->Subject = $subject; // 邮件标题信息
	$mail->Body = $content; //邮件内容
	// 发送邮件
	if (!$mail->Send()) {
		return FALSE;
	} else {
		return TRUE;
	}
}

//获取Gravatar头像 QQ邮箱取用qq头像
function getGravatar($email, $s = 96, $d = 'mp', $r = 'g', $img = false, $atts = array())
{
	preg_match_all('/((\d)*)@qq.com/', $email, $vai);
	if (empty($vai['1']['0'])) {
		$url = 'https://www.gravatar.com/avatar/';
		$url .= md5(strtolower(trim($email)));
		$url .= "?s=$s&d=$d&r=$r";
		if ($img) {
			$url = '<img src="' . $url . '"';
			foreach ($atts as $key => $val) {
				$url .= ' ' . $key . '="' . $val . '"';
			}

			$url .= ' />';
		}
	} else {
		$url = 'https://q2.qlogo.cn/headimg_dl?dst_uin=' . $vai['1']['0'] . '&spec=100';
	}
	return $url;
}

/**
 * 生成二维码
 * @param  string  $url  url连接
 * @param  integer $size 尺寸 纯数字
 */
function qrcode($url, $size = 4)
{
	Vendor('Phpqrcode.phpqrcode');
	QRcode::png($url, false, QR_ECLEVEL_L, $size, 2, false, 0xFFFFFF, 0x000000);
}

//传递数据以易于阅读的样式格式化后输出
function p($data)
{
	// 定义样式
	$str = '<pre style="display: block;padding: 9.5px;margin: 44px 0 0 0;font-size: 13px;line-height: 1.42857;color: #333;word-break: break-all;word-wrap: break-word;background-color: #F5F5F5;border: 1px solid #CCC;border-radius: 4px;">';
	if (is_bool($data)) {
		$show_data = $data ? 'true' : 'false';
	} elseif (is_null($data)) {
		$show_data = 'null';
	} else {
		$show_data = print_r($data, true);
	}
	$str .= $show_data;
	$str .= '</pre>';
	echo $str;
}

/**
 * 请求接口返回内容
 *
 * @param  string $url    [请求的URL地址]
 * @param  string $params [请求的参数]
 * @param  int    $ipost  [是否采用POST形式]
 * @return string
 */
function myCurl($url, $params = false, $ispost = 0)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在
    curl_setopt($ch, CURLOPT_HTTPHEADER, array ('Content-Type: application/json;charset=utf-8'));
    if ($ispost) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_URL, $url);
    } else {
        if ($params) {
            curl_setopt($ch, CURLOPT_URL, $url.'?'.$params);
        } else {
            curl_setopt($ch, CURLOPT_URL, $url);
        }
    }
    $response = curl_exec($ch);
    if ($response === false) {
        return 'cURL Error:' . curl_error($ch);
    }
    curl_close($ch);
    return $response;
}