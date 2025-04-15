<?php
/**
 * 首页
 */
namespace app\home\controller;
use app\home\HomeBase;
//加载数据模型
use app\common\model\Category;
use app\common\model\Article;
use app\common\model\Cloud;
use app\common\model\Goods;
use app\common\model\Links;

class Index extends HomeBase
{
    //展示
    public function index()
    {
        //获取国际云分类
        $cate_cloud = Category::where('pid', 80)->order('id', 'asc')->select();
        //获取国际云厂商推荐产品
        $cloud = [];
        foreach ($cate_cloud as $key => $value) {
            //分类下面的所有产品
            $subclass = Category::where('pid', $value['id'])->column('id');
            $all = implode(',', $subclass);
            $cloud[$key]['name'] = $value['name'];
            $cloud[$key]['alias'] = $value['alias'];
            $cloudList = Cloud::where('status', 1)->where('cid', 'in', $all)->limit(8)->select();
            foreach ($cloudList as $k => $v) {
                $cloud[$key]['cloud'][$k]['alias'] = $v['alias'];
                $cloud[$key]['cloud'][$k]['title'] = $v['title'];
                $cloud[$key]['cloud'][$k]['name'] = $v['name'];
                $cloud[$key]['cloud'][$k]['des'] = $v['des'];
                $cate = Category::find($v['cid']);
                $cloud[$key]['cloud'][$k]['classname'] = $cate['name'];
                $cloud[$key]['cloud'][$k]['cate'] = $cate['alias'];
                $cloud[$key]['cloud'][$k]['cname'] = str_replace('-', ' ', $v['alias']);
            }

        }

        //获取独立服务器分类
        $cate_stand = Category::where('pid', 59)->order('id', 'asc')->select();
        //获取分类推荐产品
        $stand = [];
        foreach ($cate_stand as $key => $value) {
            //分类下面的所有产品
            $subclass = Category::where('pid', $value['id'])->column('id');
            $all = implode(',', $subclass);
            $stand[$key]['name'] = $value['name'];
            $stand[$key]['alias'] = $value['alias'];
            $standList = Goods::where('status', 1)->where('cid', 'in', $all)->limit(8)->select();
            foreach ($standList as $k => $v) {
                $stand[$key]['stand'][$k]['alias'] = $v['alias'];
                $stand[$key]['stand'][$k]['title'] = $v['title'];
                $stand[$key]['stand'][$k]['cpu'] = $v['cpu'];
                $stand[$key]['stand'][$k]['ram'] = $v['ram'];
                $stand[$key]['stand'][$k]['hdd'] = $v['hdd'];
                $stand[$key]['stand'][$k]['bandwidth'] = $v['bandwidth'];
                $stand[$key]['stand'][$k]['ips'] = $v['ips'];
                $cate = Category::find($v['cid']);
                $stand[$key]['stand'][$k]['classname'] = $cate['name'];
                $stand[$key]['stand'][$k]['cate'] = $cate['alias'];
            }

        }
        
        //获取最新文章
        $article = Article::order('id', 'desc')->limit(30)->select();
        //获取内容图片
        foreach ($article as $key => $value) {
            $cate = Category::find($value['cid']);
            $article[$key]['classname'] = $cate['name'];
            $article[$key]['cate'] = $cate['alias'];
            $pattern="/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/";
            preg_match_all($pattern, $value["content"], $match);
            $article[$key]['headimg'] = isset($match[1][0])?$match[1][0]:"/static/aliyun/images/1.png";
        }

        //获取友情链接
        $links = Links::order('id', 'desc')->select();

        return view('', [
            'cate_cloud' => $cate_cloud,
            'cloud' => $cloud,
            'cate_stand' => $cate_stand,
            'stand' => $stand,
            'article' => $article,
            'links' => $links
        ]);
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

        return $urls;
    }
}
