<?php
namespace Swg\Composer;

/** 加密解密类 */
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
        return openssl_encrypt($encrypt_str, 'des-ecb', $secret_key?:env("COMPOSER.SECRET_KEY"));
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
        return openssl_decrypt($decrypt_str, 'des-ecb',$secret_key?:env("COMPOSER.SECRET_KEY"));
    }
}