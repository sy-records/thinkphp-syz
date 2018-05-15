<?php
namespace Vendor\Weixin;

class WechatPayBank
{
    protected $mch_appid;
    protected $mch_id;
    protected $key;
    protected $certificate_path;

    public function __construct($params){
        $this->mch_appid = $params['appid'];
        $this->mch_id = $params['mchid'];
        $this->key = $params['apiKey'];
        $this->certificate_path = $params['certificate_path'];
    }
    /**
     * [提现到微信零钱]
     * @param  [String] $openid             [用户openid]
     * @param  [int]    $amount             [企业付款金额，单位为分]
     * @param  [String] $partner_trade_no   [商户订单号，需保持唯一性(只能是字母或者数字，不能包含有符号)]
     */
    public function transfer($openid, $amount, $partner_trade_no)
    {
        $transfer = [
            'mch_appid'        => $this->mch_appid,
            'mchid'            => $this->mch_id,
            'nonce_str'        => self::createNonceStr(),
            'partner_trade_no' => $partner_trade_no,
            'openid'           => $openid,
            'check_name'       => 'NO_CHECK',
            'amount'           => $amount,
            'desc'             => "提现处理成功!",
            'spbill_create_ip' => $_SERVER["REMOTE_ADDR"]
        ];
        $transfer['sign'] = self::getSign($transfer, $this->key);

        $xml = self::arrayToXml($transfer);
        $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers";
        $data = self::curl_post_ssl($url, $xml, $this->certificate_path);
        return self::xmlToArray(strstr($data, '<xml>'));
    }

    /**
     *
     * [到银行卡]
     * @param  [String] $bank_no            [收款方银行卡号]
     * @param  [String] $true_name          [收款方用户名]
     * @param  [String] $bank_code          [收款方开户行ID]
     * @param  [int]    $amount             [企业付款金额，单位为分]
     * @param  [String] $partner_trade_no   [商户订单号，需保持唯一性(
     */
    public function paybank($bank_no, $true_name, $bank_code, $amount, $partner_trade_no)
    {

        $paybank = [
            'mch_id'            => $this->mch_id,
            'partner_trade_no'  => $partner_trade_no,
            'nonce_str'         => self::createNonceStr(),
            'enc_bank_no'       => self::getRSA($bank_no, $this->certificate_path),
            'enc_true_name'     => self::getRSA($true_name, $this->certificate_path),
            'bank_code'         => $bank_code,
            'amount'            => intval($amount),
            'desc'              => "提现处理成功!"
        ];

        $paybank['sign'] = self::getSign($paybank, $this->key);

        $xml = self::arrayToXml($paybank);

        $url = "https://api.mch.weixin.qq.com/mmpaysptrans/pay_bank";
        $data = self::curl_post_ssl($url, $xml, $this->certificate_path);
        return self::xmlToArray(strstr($data, '<xml>'));
    }

    public function rsa()
    {
        $data = [
            'mch_id'            => $this->mch_id,
            'nonce_str'         => self::createNonceStr(),
            'sign_type' => 'MD5',
        ];
        $data['sign'] = self::getSign($data, $this->key);

        $xml = self::arrayToXml($data);
        $url = 'https://fraud.mch.weixin.qq.com/risk/getpublickey';
        $arr = self::curl_post_ssl($url, $xml, $this->certificate_path);
        dump(self::xmlToArray(strstr($arr, '<xml>')));
    }

    protected static function getRSA($string, $certificate_path){
        $publicKey = file_get_contents($certificate_path.'/pkcs8.pem');
        $encryptedBlock = '';
        openssl_public_encrypt($string,$encryptedBlock, $publicKey, OPENSSL_PKCS1_OAEP_PADDING);
        return base64_encode($encryptedBlock);
    }

    /**
     * 随机字符串
     */
    protected static function createNonceStr($length = 32)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $str = '';
        for ($i = 0; $i<$length; $i++){
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     * array转xml
     */
    protected static function arrayToXml($data)
    {
        $xml = "<xml>";
        foreach ($data as $key => $val){
            // if (is_numeric($val)) {
            $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            // } else {
            //     $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            // }
        }
        $xml .= "</xml>";
        return $xml;
    }

    /**
     * xml转array
     */
    protected static function xmlToArray($xml)
    {
        libxml_disable_entity_loader(true);
        $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $values;
    }

    /**
     * 使用证书，以post方式提交xml到对应的接口url
     */
    protected static function curl_post_ssl($url, $xml, $certificate_path)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'pem');
        curl_setopt($ch, CURLOPT_SSLCERT, $certificate_path.'/apiclient_cert.pem');
        curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'pem');
        curl_setopt($ch, CURLOPT_SSLKEY, $certificate_path.'/apiclient_key.pem');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);

        $data = curl_exec($ch);
        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
             echo "call faild, errorCode:$error\n";
            curl_close($ch);
            return false;
        }
    }

    protected static function getSign($params, $key)
    {
        ksort($params, SORT_STRING);
        $unSignParaString = self::formatQueryParaMap($params, false);
        $signStr = strtoupper(md5($unSignParaString . "&key=" . $key));
        return $signStr;
    }

    protected static function formatQueryParaMap($paraMap, $urlEncode = false)
    {
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v){
            if (null != $v && "null" != $v) {
                if ($urlEncode) {
                    $v = urlencode($v);
                }
                $buff .= $k . "=" . $v . "&";
            }
        }
        $reqPar = '';
        if (strlen($buff)>0) {
            $reqPar = substr($buff, 0, strlen($buff) - 1);
        }
        return $reqPar;
    }
}