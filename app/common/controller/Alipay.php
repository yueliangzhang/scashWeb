<?php
/**
 * 支付宝支付
 */
declare (strict_types=1);
namespace app\common\controller;
use Yansongda\Pay\Pay;
use Yansongda\Pay\Log;

class Alipay
{
	// 请求参数初始化 可以赋值的
    protected $config = [
        'app_id' => '2021004154635084',
        'notify_url' => '',
        'return_url' => '',
        'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEApTJMIWrLlLs5iUiL9sWZu+ON1D9l6hgNVMNMCopghb7GtpSvx7f0nh+tWdsAJyo3c+E8nQkML0jDW1OqFM0A8nXwk3kgPwUjcVMeCnUJUQN8EAwTBJIlrNqb14EEo/9AOgNkChXlmS6TzpLgK8cmyMS28VtMFbjsrir19HCHCiYRfAdQVxwGhaVGfJOviaRbwSt2G76F3vD2i54PVEDJgAQ2PRpJAbCy1kzM6H2rza/deWJ09kc4RzOT2hwL7iHtYHxEb5dI8ZSRWn2ix0NY2WIru6w55I/jjHVOvILG2A4nZzIqlPYC84kdX8PpA0nnSiCfEsPwTuor9d6n1abGTwIDAQAB', // 注意: 这里不是应用公钥,是支付宝公钥
        'private_key' => 'MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQClMkwhasuUuzmJSIv2xZm7443UP2XqGA1Uw0wKimCFvsa2lK/Ht/SeH61Z2wAnKjdz4TydCQwvSMNbU6oUzQDydfCTeSA/BSNxUx4KdQlRA3wQDBMEkiWs2pvXgQSj/0A6A2QKFeWZLpPOkuArxybIxLbxW0wVuOyuKvX0cIcKJhF8B1BXHAaFpUZ8k6+JpFvBK3YbvoXe8PaLng9UQMmABDY9GkkBsLLWTMzofavNr915YnT2RzhHM5PaHAvuIe1gfERvl0jxlJFafaLHQ1jZYiu7rDnkj+OMdU68gsbYDidnMiqU9gLziR1fw+kDSedKIJ8Sw/BO6iv13qfVpsZPAgMBAAECggEAVWE5l8vqjGR3yEyGinR2kHV7yEc8FVF5EmYd1xTN7lI7wgH3F/Irq84Tem2GFrfrESlIeqFCeu3QilMjhLyv0KDERnREBk5RkzCyRhVXS1fRtzmGWsZuUnhqM2erTleOim1WNyA6GoHIQuwGkJnNodaJ9xSIrnF1Yk8x77R5FvTfTyrfAAe6tGfXPfnPi6AgnPXiczfp6PfPqEhxDG2e7cqMgkUCOROoGsMps9QSZi2WTJEcL84eVCYB4lUdicGn0HyyQQuCCTnFk6cOajM2OpvkUu4psNlUDsu0+Xze+Rh50qmbREQG5ExFMBQhIWCwLUkqMJ1TJl7xucjDcFVUAQKBgQDsbcJ8+EjWD3DZoi8c4KppGdyc2ggkzvS+ayBq++bfq/qAcaC03PQP+lVpWJYdy+3iCX/BAri+IXGM5hLZFpR5yN6Ldq5MlWIDB29i6gCK6nUxW9D9ntBnXaga0s8CGhjpLTTPFSPm8lv7+esyI95WkzA+QI4D8XD949FQ6+uy4QKBgQCy3wfklJYKZjksmPbKfJlbRsKUQpn7uE0NrmWB2C4NakbxEpFd+7LVDi+05HLQcEi4Xg2vtkpKAHrgjoa4eTyKT2OoTTZa42ycAi6FYx0Sg8T1qs/F5L4eKXuMohmlIqIGJ1dXhlsnq+ZRMO54y9ECpF3haY2Q6tHg2pa7dF3PLwKBgHWOVafhpAWbg9cShy5qnxDHJSRwXKBArvyHM4U+XxtT3ahD105WlpvjMtjAjVOfdFZxtq33dnhDFLykITcDvSuYrt7VUfEOTb+H0OBzzXVFAqRaLN4OOz1KGi3MJ9W5uC0opxtYPZO1afst1r4Fi6WsZc5uNq7LPA1hW1BLHdTBAoGAJEfixPVyfYeAf1BDdozRWlc+7m2X6BvY3TaLdkvQA36UBx/aBnMgMeDfwlQ4CZlM1sCVDBfwd5QjWFrwboiAxBkYQLHwnXzVscsrfni9E91QXPgZjq039sw5OCxFAV7F+HOQXrtsz94pKDzBkhTdPyjxteAAC+3lRjNwMXOH95cCgYEAkwcadL0WRHwpSVesKiVl5KxZrcqTOI+8g5hCrCDzaTiSvZ11NCsFXpr7X+bLmjQ2xSCCkJNuekssf0xL7q1bWJ/6W6DN78SqmKQFqDaF2Oz2NwdRkm65vGPt92xBhdO/J7WmNmt3ZjLncgTYhSyrUKIIAKIOnVb5Za+EB+viCtE=',
        'log' => [ // optional
            'file' => '',
            'level' => 'info', // 建议生产环境等级调整为 info，开发环境为 debug
            'type' => 'single', // optional, 可选 daily.
            'max_file' => 30, // optional, 当 type 为 daily 时有效，默认 30 天
        ],
        'http' => [ // optional
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
            // 更多配置项请参考 [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
        ],
        //'mode' => 'dev', // optional,设置此参数，将进入沙箱模式
    ];

    // 初始化数据
    public function __construct()
    {
	    // thinkPHP 中我发现总是报文件写入权限不够, 哪怕我给了 777 仍然不行, 所以就有了下面这种写法
        $this->config['log']['file'] = app()->getRuntimePath() . 'log/alipay.log';
    }

     /**
     * 支付宝支付
     * @param trade_no 订单号
     * @param amount 订单价格
     * @param subject 订单名称
     */
    public function pay($data = [])
    {
        $order = [
            'out_trade_no' => $data['trade_no'],
            'total_amount' => $data['amount'],
            'subject' => $data['title']
        ];
        $host = config('app.hostname');
        $this->config['notify_url'] = $host.'/common/pay/notify/'.$data['trade_no'];
        $this->config['return_url'] = $host.'/common/pay/return/'.$data['trade_no'];
        $alipay = \Yansongda\Pay\Pay::alipay($this->config)->web($order);

        return $alipay->send();
    }

    /**
     * 退款
     * @param trade_no 订单号
     * @param amount 金额
     */
    public function refund($data = [])
    {
        $order = [
            'out_trade_no' => $data['trade_no'],
            'refund_amount' => $data['amount'],
        ];
        
        $result = \Yansongda\Pay\Pay::alipay($this->config)->refund($order);

        return $result;
    }

    // 前端回调
    public function returnUrl()
    {
        $alipay = \Yansongda\Pay\Pay::alipay($this->config);
        $data = $alipay->verify(); // 验签
        if (!in_array($data->trade_status, ['TRADE_SUCCESS', 'TRADE_FINISHED'])) {
            return true;
            //return $alipay->success();
        }
                
        //return $alipay->success()->send();
    }

    // 服务器回调
    public function notifyUrl()
    {
        $alipay = \Yansongda\Pay\Pay::alipay($this->config); 
        $data = $alipay->verify(); // 验签
        
        // $data->trade_status 表示订单状态,在支付宝的业务通知中，只有交易通知状态为 TRADE_SUCCESS 或 TRADE_FINISHED 时，支付宝才会认定为买家付款成功。
        // $data->out_trade_no 是我们自己之前生成的订单号
        // $data->trade_no 是支付宝返回给我们的订单号
        // 所有的信息都记录在 $config 中配置的文件中, 可以在其中查看信息,也可以查看支付宝文档 https://opendocs.alipay.com/open/203/105286
        if (!in_array($data->trade_status, ['TRADE_SUCCESS', 'TRADE_FINISHED'])) {
            return true;
            //return $alipay->success()->send();
        }
                
        //return $alipay->success()->send();
    }
}