<?php
namespace Vendor\Weixin;

/**
 * 微信模板消息
 * Class WeChatTemplete
 * @package Vendor\Weixin
 */
class WeChatTemplete
{
    protected $_appid;
    protected $key;

    protected static $url;

    /**
     * 实例对象传入微信参数
     * WeChat constructor.
     * @param $appid
     * @param $key
     */
    public function __construct($appid,$key)
    {
        $this->_appid = $appid;
        $this->_key = $key;
    }

    /**
     * 发送curl 请求
     * @param $url
     * @param null $data
     * @return mixed
     */
    private static function curlRequest($url,$data=null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        if(!empty($data)) {
            // post数据
            curl_setopt($ch, CURLOPT_POST, 1);
            // post的变量
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /**
     * 获取微信access token
     * @return mixed
     */
    public function getAccessToken()
    {
        $accessToken = S('ACCESS_TOKEN');
        if(is_null($accessToken)) {
            self::$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$this->_appid}&secret={$this->_key}";
            $tokenJson = self::curlRequest(self::$url);
            $tokenArr = json_decode($tokenJson,true);
            if(isset($tokenArr['errcode'])) {
                return false;
            }
            // 缓存$tokenArr['access_token']数据7200秒
            S('ACCESS_TOKEN',$tokenArr['access_token'],$tokenArr['expires_in']);
            $accessToken = $tokenArr['access_token'];
        }
        return $accessToken;
    }

    /**
     * 发送模板消息
     * @param $openid
     * @param $template_id
     * @param $data
     * @param null $url
     * @return bool|mixed
     */
    public function sendTemplate($openid, $template_id, $data, $url = null)
    {
        $accessToken = $this->getAccessToken();
        if(!$accessToken) {
            return false;
        }
        $msgArr["touser"] = $openid;
        $msgArr["template_id"] = $template_id;
        $msgArr["data"] = $data;
        if(!empty($url)) {
            $msgArr["url"] = $url;
        }
        $msgJson = json_encode($msgArr);
        self::$url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$accessToken;

        $result = self::curlRequest(self::$url,$msgJson);
        return json_decode($result,true);
    }



}