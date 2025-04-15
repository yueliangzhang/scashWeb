<?php
/**
 * USDT支付
 */
namespace app\common\controller;

class Bit2go
{
	//参数定义
	protected $config = [
		//'Url' => 'https://openapi.bit2go.io',
		'Url' => 'https://test-openapi.bit2go.io',
		'merchantId' => 'test_MX00001',
		'paymentKey' => 'test_payment61935da4-328f-4dfe-b0bb-3a8f3d45fb91',
		'payoutKey' => 'test_payout0e7183ac-022e-459d-924f-ff0031562a01'
	];

	//初始化
	public function __construct(){

	}

	/**
	 * 获取收银台
	 * @param data 订单数据
	 */
	public function rechargeUsd($data = [])
	{
		$data['currency'] = 'USDT';
		$data['percent_fee_paid_by_user'] = 100;
		$data['callback_url'] = config('app.hostname').'/common/pay/bit2goPaymentNotify/'.$data['order_id'];

		$url = $this->config['Url'].'/api/gateway/payment';
		$result = $this->http($url, $data, $this->config['paymentKey']);

		return $result;
	}

	/**
	 * 建立钱包
	 * @param data 创建钱包参数
	 */
	public function createWallet($data = [])
	{
		$data['network'] = 'tron';

		$url = $this->config['Url'].'/api/gateway/static/wallet';
		$result = $this->http($url, $data, $this->config['paymentKey']);

		return $result;
	}

	/**
	 * 支付回调
	 * @param data 回调数组
	 * @param sign 签名
	 */
	public function rechargeNotify($data = [], $sign = '')
	{
		//验证签名
		$newsign = $this->checkSign($data, $this->config['paymentKey'], time());
		if ($sign == $newsign) {
			return true;
		}else{
			return false;
		}
	}

	/**
	 * USDT退款
	 * @param data 退款数据
	 */
	public function refund($data = [])
	{
		$data['currency'] = 'USDT';
		$data['is_fee_paid_by_receiver'] = true;
		$data['callback_url'] = config('app.hostname').'/common/pay/bit2goPayoutNotify/'.$data['order_id'];

		$url = $this->config['Url'].'/api/gateway/payout';
		$result = $this->http($url, $data, $this->config['payoutKey']);

		return $result;
	}

	/**
	 * 退款回调
	 * @param data 回调数组
	 * @param sign 签名
	 */
	public function refundNotify($data = [], $sign = '')
	{
		//验证签名
		$newsign = $this->checkSign($data, $this->config['payoutKey'], time());
		if ($sign == $newsign) {
			return true;
		}else{
			return false;
		}
	}

	/**
	 * 生成密钥
	 * @param data 签名的数据
	 * @param key 密钥
	 * @param time 时间戳
	 */
	public function checkSign($data = [], $key = '', $time = 0)
	{
		$sign = md5(json_encode($data).$time.$key);

		return $sign;
	}

	/**
	 * 数据请求
	 * @param url 请求地址
	 * @param data 请求数据
	 * @param key 密钥
	 */
	public function http($url = '', $data = [], $key = '')
	{
		$curl = curl_init();
		//数据处理
		$time = time() + 8*3600;
		$sign = $this->checkSign($data, $key, $time);
		
		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => json_encode($data),
			CURLOPT_HTTPHEADER => array(
			    'Content-Type: application/json;charset=utf-8',
			    'merchant: '.$this->config['merchantId'],
			    'timestamp: '.$time,
			    'sign: '.$sign
			),
		));

		$response = curl_exec($curl);
		curl_close($curl);
		
		return json_decode($response, true);
	}
}