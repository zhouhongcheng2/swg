<?php
require_once "../ESTApi.php";

$ESTApi = new ESTApi("dev");//测试环境
//$ESTApi = new ESTApi("prod"); //正式环境

$data = [
    "spId" => "2000017",
    "param" => $ESTApi->jsonEncode([
        "merId" => "240200000624",
        "merPrtclNo" => "2402000006240201",
        "icbcAppid" => "10000000000003160144",
        "orderNum" => "1",
        "subOrderSplitServiceInfos" => [
            [
            "busiType" => "2",
            "classifyAmt" => "3",
            "operFlag" => "0",
            "oriTrxDate" => "2020-08-14",
            "recNum" => "1",
            "seqNo" => "240248161459000512008140001001",
            "subMerId" => "240206024239",
            "subMerPrtclNo" => "2402060242390201",
            "subOrderId" => "ma244vh3xa "
            ]
        ]
    ])
];

$res = $ESTApi->shareAllocationApply($data);
if ($res === false) {
    die($ESTApi->getError());
}
echo $res;

