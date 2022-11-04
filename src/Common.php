<?php
namespace Swg\Composer;

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
    public static function curlPost(string $url, $data, array $aHeader = array(), int $timeout = 10, bool $getCode = false)
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
    public static function curlGet(string $url,int $timeout = 10, bool $getCode = false)
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
}