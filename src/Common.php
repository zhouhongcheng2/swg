<?php

namespace Swg\Composer;

use ZipArchive;

class Common
{
    /**
     * Author: zhouhongcheng
     * datetime 2022/11/1 17:11
     * @method
     * @route
     * @param string $url
     * @param $data
     * @param array $aHeader
     * @param int $timeout
     * @param bool $getCode
     * @return array|bool|string
     */
    public static function curlPost(string $url, $data, array $aHeader = array(), int $timeout = 10, bool $getCode = false,&$error_msg=null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
        // POST数据
        curl_setopt($ch, CURLOPT_POST, 1);
        // 把post的变量加上
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $output = curl_exec($ch);
        
        if (empty($output) && !is_null($error_msg)) {
            $error_msg = curl_error($ch);
        }
        
        if ($getCode) {
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            return array('http_code' => $httpCode, 'data' => $output);
        }
        curl_close($ch);
        return $output;
    }

    /**
     * Author: zhouhongcheng
     * datetime 2022/11/1 17:19
     * @method
     * @route
     * @param string $url
     * @param int $timeout
     * @param bool $getCode
     * @return array|bool|string
     */
    public static function curlGet(string $url, int $timeout = 10, bool $getCode = false,&$error_msg=null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        //不做证书校验,部署在linux环境下请改为true
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $file_contents = curl_exec($ch);
        if ($getCode) {
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            return array('http_code' => $httpCode, 'data' => $file_contents);
        }

        if (empty($output) && !is_null($error_msg)) {
            $error_msg = curl_error($ch);
        }

        curl_close($ch);
        return $file_contents;
    }

    /**
     * 图片转 base64
     * Author: lvg
     * datetime 2022/11/2 15:23
     * @param string $image_path 文件路径
     * @param bool $is_local 是否是本地文件
     * @return false|string
     */
    public static function imageToBase64(string $image_path, bool $is_local = false)
    {
        if ($is_local) {
            if (!file_exists($image_path)) {
                return false;
            }
            if ($fp = fopen($image_path, "rb", 0)) {
                $binary = fread($fp, filesize($image_path)); // 文件读取
                fclose($fp);
                $image_data = base64_encode($binary); // 转码
            } else {
                return false;
            }
        } else {
            $image_data = file_get_contents($image_path);
        }
        return $image_data;
    }

    /**
     * 创建zip压缩包
     * Author: zhouhongcheng
     * datetime 2022/11/8 18:55
     * @method
     * @route
     * @param array $file_list 文件地址 ['a.txt','b.log']
     * @param string $file_path_name zip保存地址和文件名 /path/test.zip
     * @return bool
     */
    public static function createZip(array $file_list, string $file_path_name)
    {
        $zip = new ZipArchive();
        $zip->open($file_path_name, ZipArchive::CREATE);   //打开压缩包
        //遍历文件
        foreach ($file_list as $file) {
            $zip->addFile($file, basename($file));   //向压缩包中添加文件
        }
        return $zip->close();  //关闭压缩包
    }
}