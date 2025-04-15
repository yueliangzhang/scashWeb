<?php
/**
 * 百度小程序提交
 */
namespace app\common\controller;
use app\BaseController;

class Applet extends BaseController
{
	/**
	 * 静态资源
	 */
	protected $client_id = 'U5MFOogFOYcU0ctkNux9ltkslf7W2asP';
	protected $client_secret = '8CSL5MKfXSOG0sCzG6r8ihbXsix3km9p';
	protected $access_token = '';

	//获取access_token
	public function __construct()
	{
		$curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://openapi.baidu.com/oauth/2.0/token?grant_type=client_credentials&client_id=".$this->client_id."&client_secret=".$this->client_secret."&scope=smartapp_snsapi_base",
            CURLOPT_TIMEOUT => 30,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Accept: application/json'
            ),

        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $text = json_decode($response, true);
		$this->access_token = $text['access_token'];
	}

	/**
	 * 提交小程序地址
	 * @param urls 小程序地址
	 * @param type 0、小时收录无；1、天级收录10条；2、周级收录100条
	 */
	public function sendAddr($urls = [], $type = 0)
	{
		$data['type'] = $type;
		$data['url_list'] = implode(",", $urls);
		$api = 'https://openapi.baidu.com/rest/2.0/smartapp/access/submitsitemap/api?access_token='.$this->access_token;
		$ch = curl_init();
		$options =  array(
		    CURLOPT_URL => $api,
		    CURLOPT_POST => true,
		    CURLOPT_RETURNTRANSFER => true,
		    CURLOPT_NOBODY => false,
		    CURLOPT_POSTFIELDS => http_build_query($data),
		    CURLOPT_HTTPHEADER => array('Content-Type: application/x-www-form-urlencoded'),
		);
		curl_setopt_array($ch, $options);
		$result = curl_exec($ch);
		echo $result;
	}
}