<?php
require_once "../ESTApi.php";

$ESTApi = new ESTApi("dev");//测试环境
//$ESTApi = new ESTApi("prod"); //正式环境

$bizRequestContent = [
    "clientIp" => "127.0.0.1",
    "mchOrderNo" => "M202008161535",
    "orderNo" => "",
    "mchRefundNo" => "R202008161503",
    "totalAmount" => "5",
    "refundAmount" => "1",
    "refundDesc" => "不想要了，退货退钱",
    "storeNo" => "S001",
    "notifyUrl" => "http://ecp.sedsy.com/notify/refund",
    "extendParam1" => "111",
    "extendParam2" => "222",
    "extendParam3" => "333",
    "extendParam4" => "444",
];
$data = [
    "appId" => "100000000000000",
    "timestamp" => date("YmdHis000"),
    "msgId" => uniqid("", true),
    "signType" => "RSA2",
    "encryptType" => "AES",
    "bizRequestContent" => $ESTApi->jsonEncode($bizRequestContent)
];


$res = $ESTApi->refundCreate($data);
if($res === false){
    die($ESTApi->getError());
}
echo $res;

