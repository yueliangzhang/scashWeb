<?php
// 应用公共文件
if (!function_exists('list_to_tree')) {
    /**
     * 把返回的数据集转换成Tree
     * @param $list 要转换的数据集
     * @param bool $disabled 渲染下拉树xmSelect时，有子类不可选择，默认可选
     * @param string $pk
     * @param string $pid
     * @param string $children 有子类时添加children数组
     * @param int $root
     * @return array
     */
    function list_to_tree($list, $disabled = false, $pk='id', $pid = 'pid', $children = 'children', $root = 0)
    {
        // 创建Tree
        $tree = array();
        if(is_array($list)) {
            // 创建基于主键的数组引用
            $refer = array();
            foreach ($list as $key => $data) {
                $refer[$data[$pk]] =& $list[$key];
            }

            foreach ($list as $key => $data) {
                // 判断是否存在parent
                $parentId =  $data[$pid];
                if ($root == $parentId) {
                    $tree[] =& $list[$key];
                }else{
                    if (isset($refer[$parentId])) {
                        $parent =& $refer[$parentId];
                        $parent[$children][] =& $list[$key];
                        $disabled ? $parent['disabled'] = true : '';
                    }
                }
            }
        }
        return $tree;
    }
}

if (!function_exists('getTree')) {
    /**
     * 通过PID查询所有子分类
     * @param array 传入分类数组
     * @param pid 父分类ID
     * @param level 分类等级
     */
    function getTree($array, $pid = 0, $level = 0) {
        static $list = [];
        foreach ($array as $key => $value) {
            if ($value['id'] == $pid) {
                $list[] = $value;
            }else{
                if ($value['pid'] == $pid) {
                    //$value['level'] = $level;
                    $list[] = $value;
                    unset($array[$key]); // 移除已处理的元素，减少内存消耗
                    getTree($array, $value['id'], $level + 1); // 递归调用，查找下一级子分类
                }
            }
        }

        return $list;
    }
}

if (!function_exists('data_sign')) {
    /**
     * 数据签名认证
     * @param  array $data 被认证的数据
     * @return string       签名
     */
    function data_sign($data)
    {
        // 数据类型检测
        if (!is_array($data)) {
            $data = (array)$data;
        }
        ksort($data); // 排序
        $code = http_build_query($data); // url编码并生成query字符串
        $sign = sha1($code); // 生成签名
        return $sign;
    }
}

if (!function_exists('getAdminId')) {
    /**
     * 获取用户ID
     * @return mixed
     */
    function getAdminId()
    {
        $data = session('admin_info.admin_id');
        return $data;
    }
}

if (!function_exists('sysoplog')) {
    /**
     * 写入系统日志
     * @param $action
     * @param $content
     */
    function sysoplog($action, $content)
    {
        if (session('?admin_info')) {
            \app\mxadmin\model\OplogModel::create([
                'node' => strtolower(app('http')->getName()) . "/" . strtolower(request()->controller()) . "/" . strtolower(request()->action()),
                'geoip' => request()->ip(),
                'action' => $action,
                'content' => $content,
                'username' => session('admin_info.username')
            ]);
        }
    }
}

if (!function_exists('postBaiduPage')) {
    /**
     * 百度提交
     * @param url 链接地址
     */
    function postBaiduPage($url = '')
    {
        //获取当前URL地址
        $host = ($_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://').$_SERVER['HTTP_HOST'];
        $urls = [
            $host.$url
        ];
        
        $api = 'http://data.zz.baidu.com/urls?site='.$host.'&token=sG3mWe59pHtOoCCj';

        $ch = curl_init();
        $options =  array(
            CURLOPT_URL => $api,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => implode("\n", $urls),
            CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);

        return json_decode($result, true);
    }
}

if (!function_exists('getDomaindata')) {
    /**
     * 获取链接地址
     * @param data 标签详细
     */
    function getDomaindata($data = [])
    {
        $domain = config('app.hostname');
        switch ($data['is_goods']) {
            case 0://文章 article
                $link = '<a href="'.$domain.'/tag/'.$data['tag'].'" title="'.$data['tag'].'" target="_blank">'.$data['tag'].'</a>';
                break;
            case 1://服务器商品 goods
                $goods = \app\common\model\Goods::find($data['goods_id']);
                $link = '<a href="'.$domain.'/stand/'.$goods['alias'].'/" title="'.$data['name'].'" target="_blank">'.$data['name'].'</a>';
                break;
            case 2://公有云 cloud
                $cloud = \app\common\model\Cloud::find($data['goods_id']);
                $link = '<a href="'.$domain.'/vps/'.$cloud['alias'].'/" title="'.$data['name'].'" target="_blank">'.$data['name'].'</a>';
                break;
        }

        return $link;
    }
}

if (!function_exists('setJiami')) {
    /**
     * 数据加密
     * @param data 加密数组
     */
    function setJiami($data = [])
    {
        $key = '7OwPs21TpVMF25kPJPHza@yabr0h';
        //加密
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted = openssl_encrypt(serialize($data), 'aes-256-cbc', $key, 0, $iv);

        return base64_encode($encrypted . '::' . $iv);
    }
}

if (!function_exists('getJiemi')) {
    /**
     * 数据解密
     * @param data 加密字符串
     */
    function getJiemi($data = '')
    {
        $key = '7OwPs21TpVMF25kPJPHza@yabr0h';
        //解密
        list($encryptedData, $iv) = explode('::', base64_decode($data), 2);
        $string = openssl_decrypt($encryptedData, 'aes-256-cbc', $key, 0, $iv);
        $data = unserialize($string);
        return $data;
    }
}

if (!function_exists('delPunctuation')) {
    // 去除标点符号
    function delPunctuation($text = '') {
        // 定义一个包含所有需要去除的标点符号的数组
        $punctuation = array("\"", "'", ".", ",", ";", ":", "!", "?", "(", ")", "_", "[", "]", "{", "}", "|", "/", "\\", "^", "~", "`", "+", "=", "<", ">", "&", "@", "#", "$", "%", "*");
        $filtered_text = str_replace($punctuation, "", $text);

        return $filtered_text;
    }
}

if (!function_exists('submitIndexNow')) {
    // IndexNow数据提交
    function submitIndexNow($url = '')
    {
        $host = ($_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://').$_SERVER['HTTP_HOST'];
        $data = [
            'host' => config('app.hostname'),
            'key' => 'a5d130909c8040e7910e4e45963cb9ee',
            'keyLocation' => config('app.hostname').'/a5d130909c8040e7910e4e45963cb9ee.txt',
            'urlList' => [
                $host.$url
            ]
        ];

        // 使用 cURL 发起 POST 请求
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.indexnow.org');  // 设置请求的 URL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  // 设置返回响应的内容
        curl_setopt($ch, CURLOPT_POST, true);  // 设置为 POST 请求
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));  // 将数据转换为 JSON 格式并作为请求内容
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json; charset=utf-8'  // 设置请求头，指明发送 JSON 数据
        ]);

        // 执行请求并获取响应
        $response = curl_exec($ch);

        // 检查请求是否成功
        if(curl_errno($ch)) {
            // 如果请求失败，记录错误信息
            echo 'cURL Error: ' . curl_error($ch);
        } else {
            // 如果请求成功，打印响应内容
            echo 'Response from IndexNow: ' . $response;
        }

        // 关闭 cURL 句柄
        curl_close($ch);
    }
}