<?php
/**
 * 公共函数
 * @author tigger
 */

// admin应用公共文件
if (!function_exists('getMenuData')) {
    /**
     * 获取菜单数据
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    function getMenuData()
    {
        if (session('admin_info.is_admin') == 1) {
            $where = ['isnav' => 1, 'status' => 1];
        } else {
            $groupid = \app\mxadmin\model\AuthGroupAccess::where('uid', getAdminId())->column('group_id');
            $authid = implode(',', \app\mxadmin\model\AuthGroup::where('id', 'in', $groupid)->column('rules'));
            $arr = explode(',', $authid); //explode()以逗号为分割，变成一个新的数组
            $arr = array_unique($arr); //array_unique()函数移除数组中的重复的值，并返回结果数组
            $data = implode(',', $arr); //implode() 函数返回由数组元素组合成的字符串
            $where = [
                ['id', 'in', $data],
                ['isnav', '=', 1],
                ['status', '=', 1],
            ];
        }
        $menulist = \app\mxadmin\model\AuthRule::where($where)->order(['weight', 'id'])->select()->toArray();
        return $menulist;
    }
}

if (!function_exists('formatBytes')) {
    /**
     * @param $size 字节数
     * @param string $delimiter 数字和单位分隔符
     * @return string 格式化后的带单位的大小
     */
    function formatBytes($size, $delimiter = '')
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
        for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
        return round($size, 2) . $delimiter . $units[$i];
    }
}

if (!function_exists('sendMail')) {
    /**
     * 发送邮件
     * @param $to
     * @param string $subject
     * @param string $body
     * @param string $code
     * @return string
     * @throws \PHPMailer\PHPMailer\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    function sendMail($to, $subject='', $body='', $code='')
    {
        $mailset = \app\mxadmin\model\Config::getConfigData('email');
        $toemail = $to;//定义收件人的邮箱
        $mail = new PHPMailer\PHPMailer\PHPMailer();
        $mail->isSMTP();// 使用SMTP服务
        $mail->CharSet = "utf8";// 编码格式为utf8，不设置编码的话，中文会出现乱码
        $mail->Host = $mailset['host'];// 发送方的SMTP服务器地址
        $mail->SMTPAuth = true;// 是否使用身份验证
        $mail->Username = $mailset['username'];// 发送方的163邮箱用户名，就是你申请163的SMTP服务使用的163邮箱</span><span style="color:#333333;">
        $mail->Password = $mailset['password'];// 发送方的邮箱密码，注意用163邮箱这里填写的是“客户端授权密码”而不是邮箱的登录密码！</span><span style="color:#333333;">
        $mail->SMTPSecure = "ssl";// 使用ssl协议方式</span><span style="color:#333333;">
        $mail->Port = $mailset['port'];// 163邮箱的ssl协议方式端口号是465/994

        $mail->setFrom($mailset['username'],$mailset['fullname']);// 设置发件人信息，如邮件格式说明中的发件人，这里会显示为Mailer(xxxx@163.com），Mailer是当做名字显示
        $mail->addAddress($toemail,'Wang');// 设置收件人信息，如邮件格式说明中的收件人，这里会显示为Liang(yyyy@163.com)
        $mail->addReplyTo($mailset['username'],$mailset['fullname']);// 设置回复人信息，指的是收件人收到邮件后，如果要回复，回复邮件将发送到的邮箱地址
        //$mail->addCC("xxx@163.com");// 设置邮件抄送人，可以只写地址，上述的设置也可以只写地址(这个人也能收到邮件)
        //$mail->addBCC("xxx@163.com");// 设置秘密抄送人(这个人也能收到邮件)
        //$mail->addAttachment("bug0.jpg");// 添加附件

        $mail->Subject = $subject;// 邮件标题
        $mail->Body = $body.$code;// 邮件正文
        //$mail->AltBody = "This is the plain text纯文本";// 这个是设置纯文本方式显示的正文内容，如果不支持Html方式，就会用到这个，基本无用

        if(!$mail->send()){// 发送邮件
            return "邮件错误：".$mail->ErrorInfo;// 输出错误信息
        }else{
            return '发送成功';
        }
    }
}