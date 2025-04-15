<?php
/**
 * 自动提交
 */
declare (strict_types = 1);
namespace app\sync\controller;
use app\BaseController;
//加载库文件
use app\common\model\Article;
use app\common\model\Goods;
use app\common\model\Category;
use app\common\model\Cloud;

//小程序提交
use app\common\controller\Applet;

class Index extends BaseController
{
    /*********************************************/
    //日收录提交 10
    public function index()
    {
        $yesterday = strtotime('-1 day');
        //天级级页面 只能提交10条
        $applet = new Applet();
        $urls = [];
        // 云提交
        $cloud = Cloud::whereTime('addtime', '>', $yesterday)->order('id', 'desc')->select();
        foreach ($cloud as $key => $value) {
            $urls[$key] = '/pages/vps/vps?id='.$value['id'];
        }

        if (count($urls) < 10) {
            $count = 10 - count($urls);
            // 文章提交
            $list = Article::whereTime('addtime', '>', $yesterday)->order('id', 'desc')->limit($count)->select();
            foreach ($list as $key => $value) {
                array_push($urls, '/pages/article/article?id='.$value['id']);
            }
        }

        $applet->sendAddr($urls, 1);

    }

    //周收录提交 100
    public function week()
    {
        $urls = [];
        //云分类
        $cloud_cate = Category::where('pid', 80)->order('id', 'desc')->select();
        foreach ($cloud_cate as $key => $value) {
            $child = Category::where('pid', $value['id'])->order('id', 'desc')->select();
            foreach ($child as $k => $v) {
                array_push($urls, '/pages/cloudlist/cloudlist?cid='.$v['id']);
                array_push($urls, '/pages/newslist/newslist?cid='.$v['id']);
            }
        }

        //服务器分类
        $stand_cate = Category::where('pid', 59)->order('id', 'desc')->select();
        foreach ($stand_cate as $key => $value) {
            $child = Category::where('pid', $value['id'])->order('id', 'desc')->select();
            foreach ($child as $k => $v) {
                array_push($urls, '/pages/standlist/standlist?cid='.$v['id']);
                array_push($urls, '/pages/newslist/newslist?cid='.$v['id']);
            }
        }

        //云
        $cloud = Cloud::order('id', 'asc')->select();
        foreach ($cloud as $key => $value) {
            array_push($urls, '/pages/vps/vps?id='.$value['id']);
        }

        //服务器
        $stand = Goods::order('id', 'asc')->select();
        foreach ($stand as $key => $value) {
            array_push($urls, '/pages/stand/stand?id='.$value['id']);
        }

        //文章
        $article = Article::order('id', 'asc')->select();
        foreach ($article as $key => $value) {
            array_push($urls, '/pages/article/article?id='.$value['id']);
        }

        foreach ($urls as $key => $value) {
            echo $value.'<br>';
        }
    }

    //百度API提交
    public function submitApi()
    {
        $time = date('Y-m-d');
        $day = strtotime($time." 23:59:59");
        $host = ($_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://').$_SERVER['HTTP_HOST'];
        //获取需要推送链接
        $urls = [];
        //云
        $cloud = Cloud::whereDay('addtime')->order('id', 'desc')->select();
        foreach ($cloud as $key => $value) {
            $url = $host.'/vps/'.$value['alias'].'/';
            array_push($urls, $url);
        }
        //服务器
        $stand = Goods::whereDay('addtime')->order('id', 'desc')->select();
        foreach ($stand as $key => $value) {
            $url = $host.'/stand/'.$value['alias'].'/';
            array_push($urls, $url);
        }
        //文章
        $article = Article::whereDay('addtime')->order('id', 'desc')->select();
        foreach ($article as $key => $value) {
            $url = $host.'/article/'.$value['alias'].'/';
            array_push($urls, $url);
        }

        //提交百度API
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

        print_r(json_decode($result, true));
    }

    //自动切换时间
    public function time()
    {
        $time = strtotime('2025-2-9 15:00:00');
        //时间更新
        $cloud = Cloud::whereTime('addtime', '<', $time)->order('id', 'asc')->limit(2)->select();
        foreach ($cloud as $key => $value) {
            $update = [
                'id' => $value['id'],
                'addtime' => time()
            ];
            Cloud::update($update);
        }

        $stand = Goods::whereTime('addtime', '<', $time)->order('id', 'asc')->limit(2)->select();
        foreach ($stand as $key => $value) {
            $update = [
                'id' => $value['id'],
                'addtime' => time()
            ];
            Goods::update($update);
        }
    }

    //现有动态地址
    public function active()
    {
        $urls = [];
        $host = ($_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://').$_SERVER['HTTP_HOST'];
        //云分类
        $cloud_cate = Category::where('pid', 80)->order('id', 'desc')->select();
        foreach ($cloud_cate as $key => $value) {
            $child = Category::where('pid', $value['id'])->order('id', 'desc')->select();
            foreach ($child as $k => $v) {
                $url = [
                    'title' => $v['name'],
                    'ptitle' => $value['name'],
                    'url' => $host.'/Cloud/'.$v['alias'].'/'
                ];
                array_push($urls, $url);
            }
        }

        //服务器分类
        $stand_cate = Category::where('pid', 59)->order('id', 'desc')->select();
        foreach ($stand_cate as $key => $value) {
            $child = Category::where('pid', $value['id'])->order('id', 'desc')->select();
            foreach ($child as $k => $v) {
                $url = [
                    'title' => $v['name'],
                    'ptitle' => $value['name'],
                    'url' => $host.'/Server/'.$v['alias'].'/'
                ];
                array_push($urls, $url);
            }
        }

        print_r($urls);exit;
    }
}
