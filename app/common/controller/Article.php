<?php
/**
 * 文章信息提取
 * 1、提取标签
 */
namespace app\common\controller;
use app\BaseController;

class Article extends BaseController
{
	/**
	 * 静态资源
	 */
	protected $client_id = 'Qj6CLLeCzxDVYkmBvC9jT8hm';
	protected $client_secret = 'EwomBKT14OYVCH5Lpc8N8lRIdXLU75O9';
	protected $access_token = '';

	//获取access_token
	public function __construct()
	{
		$curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://aip.baidubce.com/oauth/2.0/token?client_id=".$this->client_id."&client_secret=".$this->client_secret."&grant_type=client_credentials",
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
	 * 获取文章标签
	 * @param title 文章标题
	 * @param content 文章内容
	 */
	public function getTags($title = '', $content = '')
	{
	    $url = "https://aip.baidubce.com/rpc/2.0/nlp/v1/keyword?charset=UTF-8&access_token=" . $this->access_token;
	    $data = [
	    	'title' => $title,
	        "content" => $content
	    ];

	    $options = [
	        CURLOPT_URL => $url,
	        CURLOPT_POST => true,
	        CURLOPT_RETURNTRANSFER => true,
	        CURLOPT_SSL_VERIFYPEER => false,
	        CURLOPT_POSTFIELDS => json_encode($data),
	        CURLOPT_HTTPHEADER => [
	            'Content-Type: application/json',
                'Accept: application/json'
	        ],
	    ];

	    $ch = curl_init();
	    curl_setopt_array($ch, $options);
	    $response = curl_exec($ch);
	    curl_close($ch);

	    return json_decode($response, true);
	}

	/**
	 * 获取文章关键词
	 * @param content 文章内容
	 */
	public function getKeywords($content = '')
	{
		$url = "https://aip.baidubce.com/rpc/2.0/nlp/v1/txt_keywords_extraction?access_token=".$this->access_token;
		$data = [
			'text' => [$content]
		];

		$curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER  => false,
            CURLOPT_SSL_VERIFYHOST  => false,
            CURLOPT_CUSTOMREQUEST => 'POST',
            
            CURLOPT_POSTFIELDS => json_encode($data),
    
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),

        ));
        $response = curl_exec($curl);
        curl_close($curl);

	    return json_decode($response, true);
	}

}