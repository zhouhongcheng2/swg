<?php
require_once "ErrorMsg.php";
require_once "ESTConfig.php";

class EST
{
    use ErrorMsg;
    public function formatKey($priKeyOrPubKey, $keyType) {
        $keyTypeText = $keyType == "PUBLIC" ? "PUBLIC" : "PRIVATE";
        $fKey = "-----BEGIN {$keyTypeText} KEY-----\n";
        $len = strlen($priKeyOrPubKey);
        for($i = 0; $i < $len; ) {
            $fKey = $fKey . substr($priKeyOrPubKey, $i, 64) . "\n";
            $i += 64;
        }
        $fKey .= "-----END {$keyTypeText} KEY-----";
        return $fKey;
    }

    protected function sign($str) {
        $priKeyId = openssl_pkey_get_private($this->formatKey(ESTConfig::priKey, 'PRIVATE'));
        openssl_sign($str, $signature, $priKeyId, OPENSSL_ALGO_SHA256 );
        openssl_free_key($priKeyId);
        return base64_encode($signature);
    }

    public function getSign($data)
    {
        ksort($data);
        $json = $this->jsonEncode($data);
        return $this->sign($json);
    }

    protected function verify($str, $sign)
    {
        $publicKeyId = openssl_pkey_get_public($this->formatKey(ESTConfig::pubKey, 'PUBLIC'));
        $res = openssl_verify($str, base64_decode($sign), $publicKeyId, OPENSSL_ALGO_SHA256);
        openssl_free_key($publicKeyId);
        return $res;
    }

    public function isJson($json)
    {
        json_decode($json);
        return json_last_error() == 0;
    }

    public function jsonEncode($array)
    {
        //$string = is_array($array) ? (object) $array : $array;
        return json_encode($array, JSON_UNESCAPED_SLASHES + JSON_UNESCAPED_UNICODE);
    }


    protected function doPost($url, $json){
        $headers = [
            "Content-Type: application/json",
            "cache-control: no-cache"
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS,8000);//????????????
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 30000);//????????????
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);

        $response = curl_exec($ch);

        if ($response === false) {
            $msg = 'curl??????: ('.curl_errno($ch).') '. curl_error($ch);
            $this->setError($msg);
            return false;
        }
        $resInfo = curl_getinfo($ch);


        curl_close($ch);

        if($resInfo["http_code"] != 200){
            print_r($response);
            self::setError("??????????????????");
            return false;
        }

        if(!$this->isJson($response)){
            $this->setError("??????????????????:???json??????");
            return false;
        }

        return $response;
    }
}