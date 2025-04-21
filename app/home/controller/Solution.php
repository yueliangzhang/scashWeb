<?php
/**
 * 文章列表
 */
namespace app\home\controller;
use app\home\HomeBase;
//加载数据模型
use app\common\model\Article;
use app\common\model\Category;

class Solution extends HomeBase
{
    /**
     * 全展示
     */
    public function index($limit = 40)
    {
        $seo = [
            'title' => '雲計算基礎知識與技術',
            'description' => '探索人工智慧與資料分析技術，包括機器學習模型訓練（如SageMaker、Vertex AI）、大資料分析解決方案（如BigQuery、Synapse）以及資料可視化工具（如Looker、QuickSight）。',
            'info' => '了解雲網路與安全的關鍵概念，涵蓋VPC、子網、DNS、負載均衡、CDN等技術，保障您的雲服務安全。還包括IAM、KMS、SSL/TLS加密與DDoS防護等合規管理措施。'
        ];

        $page = $this->request->param('page', '', 'intval');
        $list = Article::order('id', 'desc')->paginate($limit);
        //获取内容图片
        foreach ($list as $key => $value) {
            $cate = Category::find($value['cid']);
            $list[$key]['classname'] = $cate['name'];
            $list[$key]['cate'] = $cate['alias'];
            $pattern="/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/";
            preg_match_all($pattern, $value["content"], $match);
            $default = rand(1, 5);
            $list[$key]['headimg'] = isset($match[1][0])?$match[1][0]:"/static/aliyun/images/".$default.".png";
        }

        return view('', [
            'seo' => $seo,
            'list' => $list
        ]);
    }

    /**
     * 文章分類
     */
    public function class($limit = 20)
    {
        $page = $this->request->param('page', '', 'intval');
        $name = $this->request->param('name', '', 'trim');
        $name = strtolower($name);
        $cate = Category::where('alias', $name)->find();
        $parentCate = Category::find($cate['pid']);
        $cate['parentname'] = $parentCate['name'];
        if (!empty($cate)) {
            //获取文章
            $list = Article::where('cid', $cate['id'])->order('id', 'desc')->paginate($limit);
            //获取内容图片
            foreach ($list as $key => $value) {
                $cate = Category::find($value['cid']);
                $list[$key]['classname'] = $cate['name'];
                $list[$key]['cate'] = $cate['alias'];
                $pattern="/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/";
                preg_match_all($pattern, $value["content"], $match);
                $default = rand(1, 5);
                $list[$key]['headimg'] = isset($match[1][0])?$match[1][0]:"/static/aliyun/images/".$default.".png";
            }

            return view('list', [
                'cate' => $cate,
                'list' => $list
            ]);
        } else {
            $this->error('頁面不存在！');
        }
    }
}
