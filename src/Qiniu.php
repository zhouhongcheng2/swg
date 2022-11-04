<?php

namespace Swg\Composer;

use Exception;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;

/** 七牛云工具类 */
class Qiniu
{
    /** @var int 本地文件上传 */
    CONST FILE_TYPE_LOCAL = 0;

    /** @var int 二进制文件数据 */
    CONST FILE_TYPE_STREAM = 1;

    /**
     * 七牛云上传
     * Author: zhouhongcheng
     * datetime 2022/11/2 19:22
     * @method
     * @route
     * @param mixed $file_data 本地文件路径/二进制数据
     * @param int $file_type 0-文件上传 1-二进制文件上传（目前仅支持图片上传）
     * @param string $file_suffix 二进制文件后缀
     * @return false|string
     * @throws Exception
     */
    public function uploadQiniu($file_data, int $file_type = self::FILE_TYPE_LOCAL,string $file_suffix = 'png')
    {
        // require_once 'sdk/qiniu/autoload.php';
        require_once root_path() .'vendor/swg/composer/sdk/qiniu/autoload.php';
        // 构建鉴权对象
        $auth = new Auth(env("QINIU.ACCESS_KEY"), env("QINIU.SECRET_KEY"));
        // 生成上传 Token
        $token = $auth->uploadToken(env("QINIU.BUCKET"));
        $qiniu_path = date("Ym") . '/' . date("d") . '/' .date('His') . mt_rand(0, 99999);
        // 初始化 UploadManager 对象并进行文件的上传。
        $uploadMgr = new UploadManager();
        if ($file_type == 0){
            // 要上传文件的本地路径
            $qiniu_path .= strrchr($file_data, '.');
            // 调用 UploadManager 的 putFile 方法进行文件的上传。
            list($ret, $err) = $uploadMgr->putFile($token, $qiniu_path, $file_data, null, 'application/octet-stream', true, null, 'v2');
        }elseif ($file_type == 1){
            $qiniu_path .= '.' . $file_suffix;
            list($ret, $err) = $uploadMgr->put($token, $qiniu_path, $file_data);
        }else{
            return false;
        }
        if ($err !== null) return false;
        return env("QINIU.CDN_URL")."/".$ret['key'];
    }
}