<?php
/**
 * 邮件发送
 */
namespace app\common\controller;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use app\BaseController;

class Email extends BaseController
{
	//初始化
	public function __construct(){

	}
	/**
	 * 邮件发送
	 * @param to 邮件地址
	 * @param subject 邮件标题
	 * @param body 邮件内容
	 */
	public static function sendmail($to = '', $subject = '', $body = '')
	{
		$mail = new PHPMailer(true);
		try {
			//加载配置
			$config = config('email');
			$mail->isSMTP();
			$mail->CharSet 	  = 'UTF-8';
			$mail->Host       = $config['host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $config['username'];
            $mail->Password   = $config['password'];
            $mail->Port       = $config['port'];
            $mail->SMTPSecure = $config['encryption'];

            $mail->setFrom($config['from'], $config['from_name']);
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            $mail->send();

            return true;
		} catch (Exception $e) {
			exception($mail->ErrorInfo, 500);
            return false;
		}
	}
}