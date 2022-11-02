<?php

namespace Swg\Composer;

use Exception;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;

/** 七牛云工具类 */
class Qiniu
{

    /**
     * 七牛云上传
     * Author: zhouhongcheng
     * datetime 2022/11/2 19:22
     * @method
     * @route
     * @param mixed $file_data
     * @param int $file_type 0-文件上传 1-二进制文件上传（目前仅支持图片上传）
     * @return false
     * @throws Exception
     */
    public function uploadQiniu($file_data,int $file_type = 0)
    {
        require_once 'sdk/qiniu/autoload.php';
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
            $qiniu_path .= '.png';
            list($ret, $err) = $uploadMgr->put($token, $qiniu_path, $file_data);
        }else{
            return false;
        }
        if ($err !== null) return false;
        return env("QINIU.CDN_URL")."/".$ret['key'];
    }
}