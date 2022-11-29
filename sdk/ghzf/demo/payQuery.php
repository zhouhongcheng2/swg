<?php
require_once "../ESTApi.php";

$ESTApi = new ESTApi("dev");//测试环境
//$ESTApi = new ESTApi("prod"); //正式环境

$bizRequestContent = [
    "mchOrderNo" => "M202008161535",
    "orderNo" => ""
];

$data = [
    "appId" => "100000000000000",
    "timestamp" => date("YmdHis000"),
    "msgId" => uniqid("", true),
    "signType" => "RSA2",
    "encryptType" => "AES",
    "bizRequestContent" => $ESTApi->jsonEncode($bizRequestContent)
];

$res = $ESTApi->payQuery($data);
if($res === false){
    die($ESTApi->getError());
}
echo $res;

