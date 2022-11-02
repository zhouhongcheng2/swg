<?php
require_once 'vendor/autoload.php';
$en = new \Swg\Composer\Encrypt();
$str = '123645479879周洪成';
$en_str = $en->encrypt($str);
echo $en_str;
echo PHP_EOL;
echo $en->decrypt($en_str);
echo PHP_EOL;
echo $str;