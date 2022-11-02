<?php
namespace Swg\Composer;

/** 加密、解密、密码生成 */
class Encrypt
{
    /**
     * 加密函数
     * Author: zhouhongcheng
     * datetime 2022/11/1 17:28
     * @method
     * @route
     * @param string $encrypt_str 加密原始参数
     * @param string $secret_key 加密秘钥
     * @return string
     */
    public function encrypt(string $encrypt_str,string $secret_key = ''): string
    {
        return openssl_encrypt($encrypt_str, 'des-ecb', $secret_key?:env("PASSWORD.SECRET_KEY"));
    }

    /**
     * 解密函数
     * Author: zhouhongcheng
     * datetime 2022/11/1 17:28
     * @method
     * @route
     * @param string $decrypt_str 解密字符串
     * @param string $secret_key 解密秘钥
     * @return string
     */
    public function decrypt(string $decrypt_str,string $secret_key = ''): string
    {
        return openssl_decrypt($decrypt_str, 'des-ecb',$secret_key?:env("PASSWORD.SECRET_KEY"));
    }

    /**
     * 不可逆密码生成
     * Author: zhouhongcheng
     * datetime 2022/11/2 19:52
     * @method
     * @route
     * @param string $password 用户传入的密码
     * @param string $salt 加密盐
     * @param int $length 加密盐长度
     * @return array
     */
    public function createPassword(string $password,string $salt = '',int $length = 6)
    {
        // 密码字符集，可任意添加你需要的字符
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_ []{}<>~`+=,.;:/?|';
        if ($salt){
            return [
                'password'  =>  md5($salt.$password.$salt),
                'salt'      =>  $salt
            ];
        }
        $salt = '';
        for ( $i = 0; $i < $length; $i++ )
        {
            // 这里提供两种字符获取方式
            // 第一种是使用 substr 截取$chars中的任意一位字符；
            // 第二种是取字符数组 $chars 的任意元素
            // $password .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
            $salt .= $chars[ mt_rand(0, strlen($chars) - 1) ];
        }
        return [
            'password'  =>  md5($salt.$password.$salt),
            'salt'      =>  $salt
        ];
    }
}