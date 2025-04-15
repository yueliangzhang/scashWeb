<?php
/**
 * 前置验证
 */
namespace app\spider;
use app\BaseController;

class SpiderBase extends BaseController
{
    // 密钥，用于生成和验证JWT
    private $key = "ivZuFTBbpOf4N3gdzSxEnqhX62al7y5j";

    /**
     * 初始化 判断token是否失效
     */
    public function initialize()
    {
        //判断TOKEY是否存在
        $this->validateRequest();
    }

    /**
     * 验证小程序请求的安全性
     * @return bool 返回验证结果，true表示验证通过，false表示验证失败
     */
    public function validateRequest()
    {
        $data = input('param.');
        //判断是否传递token
        if (!isset($data['sign'])) {
            return $this->result('', 500, '非法TOKEN请求');
        }

        //判断token是否合理
        $prestr = createLinkstring(argSort(paraFilter($data)));
        if (!md5Verify($prestr, $data['sign'], $this->key)) {
            return $this->result('', 500, '签名验证错误');
        }
    }

    /**
     * 获取当前汇率
     */
    public function getRate()
    {
        $rate = \app\mxadmin\model\Config::getConfigData('setconfig');
        return $rate['rate'];
    }
}