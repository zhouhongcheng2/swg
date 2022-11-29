<?php
require_once "../ESTApi.php";

$ESTApi = new ESTApi("dev");//测试环境
//$ESTApi = new ESTApi("prod"); //正式环境

$data = [
    "spId" => "2000017",
    "param" => $ESTApi->jsonEncode([
        "icbcAppId" => "10000000000003160144",
        "merTrnscSerno" => "26h0j5q7ag",
        "orderId" => "240248161459000522008110000001",
        "orderNoType" => "1",
        "queryType" => "1",
        "serNo" => "240200000624",
        "subPrtiId" => "240206024239"
    ]),
];

$res = $ESTApi->shareAllocationQuery($data);
if ($res === false) {
    die($ESTApi->getError());
}
echo $res;

