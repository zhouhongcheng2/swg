<?php
require_once "../ESTApi.php";

//$ESTApi = new ESTApi("dev");//测试环境
$ESTApi = new ESTApi("prod"); //正式环境

$bizRequestContent = [
    "clientIp" => "127.0.0.1",
    "mchOrderNo" => "SWG".mt_rand(100000,999999), //需唯一值
    "totalAmount" => "1",
    "payAmount" => "1",
    "goodsName" => "旺仔小馒头",
    "goodsDesc" => $ESTApi->jsonEncode([
        [
            "goodsId" => "001",
            "goodsName" => "旺旺雪饼",
            "goodsDesc" => "旺旺雪饼2",
            "quantity" => "1",
            "price" => "99",
        ]
    ]),
    "payWayCode" => "20001",
    "notifyUrl" => "https://www.baidu.com",
    "returnUrl" => "https://www.baidu.com",
    "expireTime" => "3600",
    "deviceNo" => "DV001",
    "storeNo" => "S001",
    "extra" => "{'accessType':'9','payMode':'9','openId':'oZLqa5H9WaInFLJrkZGtW1jTHIlo','appId':'wx18c238a20bf88793'}",
    "extendParam1" => "001",
    "extendParam2" => "002",
    "extendParam3" => "003",
    "extendParam4" => "004"
];

$data = [
    "appId" => "10000000000003186635",
    "timestamp" => date("YmdHis000"),
    "msgId" => uniqid("", true),
    "signType" => "RSA2",
    "encryptType" => "AES",
    "bizRequestContent" => $ESTApi->jsonEncode($bizRequestContent)
];

$res = $ESTApi->payCreate($data);
if($res === false){
    die($ESTApi->getError());
}
var_dump($res);exit;

file_put_contents("./a.txt",json_decode($res,true)['bizResponseContent']['result']['payParam']);


