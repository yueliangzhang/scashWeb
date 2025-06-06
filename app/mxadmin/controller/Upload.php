<?php
/**
 * 上传
 * @author tigger
 */
declare (strict_types = 1);

namespace app\mxadmin\controller;

use app\mxadmin\AdminBase;
use think\facade\Filesystem;
use app\mxadmin\model\Config;
// 引入鉴权类
use Qiniu\Auth;
// 引入上传类
use Qiniu\Storage\UploadManager;
// 引入空间管理类
use Qiniu\Storage\BucketManager;
use OSS\OssClient;
use OSS\Core\OssException;

class Upload extends AdminBase
{
    /**
     * 无需权限判断的方法
     * @var array
     */
    protected $noNeedAuth = ['local', 'qiniu', 'delFile', 'aliyun', 'upload_editor_same', 'upload', 'upload_file_same'];

    /**
     * 上传到本地
     * @param $file
     * @param $path
     * @param $sucMsg
     * @param $errMsg
     * @param string $editor
     * @return \think\response\Json|void
     */
    public function local($file, $path, $sucMsg, $errMsg, $editor = '')
    {
        $savename = Filesystem::disk('public')->putFile($path, $file);
        $filepath = Filesystem::getDiskConfig('public','url').'/'.str_replace('\\','/',$savename);
        $url = $filepath;
        if ($editor == '') {
            if ($savename == true) {
                return $this->success($sucMsg, '', ['filePath' => $url]);
            } else {
                return $this->error($errMsg);
            }
        } else {
            return json(['location' => $url]);
        }
    }

    /**
     * 上传到七牛云
     * @param $file
     * @param $accesskey
     * @param $secretkey
     * @param $bucket
     * @param $domain
     * @param $msg
     * @param string $editor
     * @return \think\response\Json|void
     * @throws \Exception
     */
    public function qiniu($file, $accesskey, $secretkey, $bucket, $domain, $msg, $editor = '')
    {
        // 构建鉴权对象
        $auth = new Auth($accesskey, $secretkey);
        // 生成上传 Token
        $token = $auth->uploadToken($bucket);
        // 要上传文件的本地路径
        $filePath = $file->getRealPath();
        // 上传到七牛存储后保存的文件名
        $key = date("Ymd",time()).'/'.$file->md5().'.'.$file->getOriginalExtension();
        // 初始化 UploadManager 对象并进行文件的上传。
        $uploadMgr = new UploadManager();
        // 调用 UploadManager 的 putFile 方法进行文件的上传，该方法会判断文件大小，进而决定使用表单上传还是分片上传，无需手动配置。
        list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
        if ($err !== null) {
            return $this->error('上传失败');
        } else {
            if ($editor == '') {
                return $this->success($msg, '', ['filePath' => $domain.'/'.$ret['key']]);
            } else {
                return json(['location' => $domain.'/'.$ret['key']]);
            }
        }
    }

    /**
     * 上传到阿里云
     * 
     */
    public function aliyun($file, $accesskey, $secretkey, $bucket, $domain, $type = '')
    {
        try {
            $accessKeyId = $accesskey;
            $accessKeySecret =  $secretkey;//阿里云后台获取秘钥
            $endpoint = $domain;
            $bucket = $bucket;
            // 要上传文件的本地路径
            $filePath = $file->getRealPath();
            //实例化对象 将配置传入
            $ossClient = new OssClient($accesskey, $secretkey, $endpoint);
            $pathName = 'storage/'.$type.'/'.date("Ymd",time()).'/'.$file->md5().'.'.$file->getOriginalExtension();
            //执行阿里云上传 bucket名称,上传的目录,文件
            $result = $ossClient->uploadFile($bucket, $pathName, $filePath);
            $url = $result['info']['url'];
            //将结果输出
            if ($type == 'editor') {
                json_exit(['location' => $url, 'flag' => 1, 'msg' => '上传成功！']);
            }else{
                return $this->success('上传成功！', '', ['filePath' => $url]);
            }
        } catch (OssException $e) {
            return $this->error('上传失败');
        }
    }

    /**
     * 图片上传
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function upload()
    {
        $file = request()->file('file');
        $storage = Config::getConfigData('storage');
        if ($storage['engine'] == 1) { //本地存储
            $this->local($file, 'images', '图片上传成功', '图片上传失败');
        } elseif ($storage['engine'] == 2) { //七牛云存储
            $this->qiniu($file, $storage['accesskey'], $storage['secretkey'], $storage['bucket'], $storage['domain'], '图片上传成功');
        } elseif ($storage['engine'] == 3) { //阿里云存储
            $this->aliyun($file, $storage['AliAccessKey'], $storage['AliSecretKey'], $storage['Alibucket'], $storage['Alidomain'], 'images');
        }
    }

    /**
     * 文件上传
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function upload_file_same()
    {
        $file = request()->file('file');
        $storage = Config::getConfigData('storage');
        if ($storage['engine'] == 1) { //本地存储
            $this->local($file, 'file', '文件上传成功', '文件上传失败');
        } elseif ($storage['engine'] == 2) { //七牛云存储
            $this->qiniu($file, $storage['accesskey'], $storage['secretkey'], $storage['bucket'], $storage['domain'], '文件上传成功');
        } elseif ($storage['engine'] == 3) { //阿里云存储
            $this->aliyun($file, $storage['AliAccessKey'], $storage['AliSecretKey'], $storage['Alibucket'], $storage['Alidomain'], 'file');
        }
    }

    /**
     * 编辑器上传
     * @return \think\response\Json|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function upload_editor_same()
    {
        $file = request()->file('file');
        $storage = Config::getConfigData('storage');
        if ($storage['engine'] == 1) { //本地存储
            return $this->local($file, 'images', '', '', 1);
        } elseif ($storage['engine'] == 2) { //七牛云存储
            return $this->qiniu($file, $storage['accesskey'], $storage['secretkey'], $storage['bucket'], $storage['domain'], '', 1);
        } elseif ($storage['engine'] == 3) { //阿里云存储
            $this->aliyun($file, $storage['AliAccessKey'], $storage['AliSecretKey'], $storage['Alibucket'], $storage['Alidomain'], 'editor');
        }
    }

    public function delFile($fileName)
    {
        $storage = Config::getConfigData('storage');
        // 控制台获取密钥：https://portal.qiniu.com/user/key
        $accessKey = $storage['accesskey'];
        $secretKey = $storage['secretkey'];
        $bucket = $storage['bucket'];

        $auth = new Auth($accessKey, $secretKey);

        $config = new \Qiniu\Config();
        $bucketManager = new BucketManager($auth, $config);

        // 删除指定资源，参考文档：https://developer.qiniu.com/kodo/api/1257/delete
        $key = $fileName;

        $err = $bucketManager->delete($bucket, $key);
    }
}
