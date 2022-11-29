<?php
include_once 'AES.php';
include_once 'IcbcConstants.php';
	class IcbcEncrypt{
		public static function encryptContent($content, $encryptType, $encryptKey, $charset){
			if(IcbcConstants::$ENCRYPT_TYPE_AES == $encryptType){
				return AES::AesEncrypt($content,base64_decode($encryptKey));
			}else{
				throw new Exception("Only support AES encrypt!");
			}
		}

		public static function decryptContent($encryptedContent, $encryptType, $encryptKey, $charset){
			if(IcbcConstants::$ENCRYPT_TYPE_AES == $encryptType){
				return AES::AesDecrypt($encryptedContent,base64_decode($encryptKey));
			}else{
				throw new Exception("Only support AES decrypt!");
			}
		}
	}
?>