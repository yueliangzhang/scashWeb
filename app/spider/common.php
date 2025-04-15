<?php
//公共函数

if (!function_exists('paraFilter')) {
	/**
	 * 去除数组中的空值和签名参数
	 * @param data 签名参数组
	 * return 去掉空值与签名参数后的新签名参数组
	 */
	function paraFilter($data = [])
	{
		$para_filter = [];
		foreach ($data as $key => $value) {
			if($key == "sign" || $key == "sign_type" || $key =="extra" || empty($value) ) continue;
            else $para_filter[$key] = $data[$key];
		}

		return $para_filter;
	}
}

if (!function_exists('argSort')) {
	/**
	 * 对数组排序
	 * @param data 排序前的数组
	 * return 排序后的数组
	 */
	function argSort($data = [])
	{
		ksort($data);
		reset($data);

		return $data;
	}
}

if (!function_exists('createLinkstring')) {
	/**
     * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
     * @param $para 需要拼接的数组
     * return 拼接完成以后的字符串
     */
    function createLinkstring($para) {
        $arg  = "";
        foreach ($para as $key => $value) {
            $arg.=$key."=".$value."&";
        }
        //去掉最后一个&字符
        $arg = substr($arg, 0, strlen($arg)-1);
        
        return $arg;
    }
}

if (!function_exists('md5Verify')) {
    /**
     * 验证签名
     * @param $prestr 需要签名的字符串
     * @param $sign 签名结果
     * @param $key 私钥
     * return 签名结果
     */
    function md5Verify($prestr, $sign, $key) {
        $prestr = $prestr . '&key=' . $key;
        $mysgin = md5($prestr);

        if($mysgin == $sign) {
            return true;
        }else {
            return false;
        }
    }
}