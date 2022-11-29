<?php
require_once "EST.php";

class ESTApi extends EST
{
    protected $urlPre = "";

    function __construct($env)
    {
        empty($env) && die("未设置环境变量");
        if($env==="prod"){
            $this->urlPre = "https://rfbp.gz.icbc.com.cn/";
        }else{
            $this->urlPre = "https://ecp.sedsy.com/";
        }
    }

    /**
     * 响应头验签
     * @param $res
     * @return bool
     */
    public function verifyResponse($res){
        $resArr = json_decode($res, true);
        if(!isset($resArr['bizResponseContent']['code'])){
            $this->setError("返回数据格式异常!");
            return false;
        }

        if($resArr['bizResponseContent']['code'] != "OK"){
            return true;
        }
        $resSign = $resArr["sign"];
        if(isset($resArr["bizResponseContent"])){
            $resArr["bizResponseContent"] = $this->jsonEncode($resArr["bizResponseContent"]);
        }
        unset($resArr["sign"]);
        ksort($resArr);
        if($this->verify($this->jsonEncode($resArr), $resSign) !== 1){
            $this->setError("返回数据签名校验失败!");
            return false;
        }
        return true;
    }

    public function verifyRequest($res){
        $resArr = $res;
        if(!isset($resArr['bizRequestContent']['code'])){
            $this->setError("返回数据格式异常!");
            return false;
        }

        if($resArr['bizRequestContent']['code'] != "OK"){
            return true;
        }
        $resSign = $resArr["sign"];
        if(isset($resArr["bizRequestContent"])){
            $resArr["bizRequestContent"] = $this->jsonEncode($resArr["bizRequestContent"]);
        }
        unset($resArr["sign"]);
        ksort($resArr);
        if($this->verify($this->jsonEncode($resArr), $resSign) !== 1){
            $this->setError("返回数据签名校验失败!");
            return false;
        }
        return true;
    }


    /**
     * 统一下单
     * @param array $data
     * @return bool|string
     */
    public function payCreate($data)
    {
        unset($data["mchId"], $data["sign"]);
        $data["mchId"] = ESTConfig::mchId;
        $data["sign"] = $this->getSign($data);
        $res = $this->doPost($this->urlPre.ESTConfig::payCreateUrl, $this->jsonEncode($data));
        if(!$this->verifyResponse($res)){
            return false;
        }
        return $res;
    }


    /**
     * 支付订单查询接口
     * @param array $data
     * @return bool|string
     */
    public function payQuery($data)
    {
        unset($data["mchId"], $data["sign"]);
        $data["mchId"] =ESTConfig::mchId;
        $data["sign"] = $this->getSign($data);
        $res = $this->doPost($this->urlPre.ESTConfig::payQueryUrl, $this->jsonEncode($data));
        if(!$this->verifyResponse($res)){
            return false;
        }
        return $res;
    }


    /**
     * 统一退款
     * @param array $data
     * @return bool|string
     */
    public function refundCreate($data)
    {
        unset($data["mchId"], $data["sign"]);
        $data["mchId"] = ESTConfig::mchId;
        $data["sign"] = $this->getSign($data);
        $res = $this->doPost($this->urlPre.ESTConfig::refundCreateUrl, $this->jsonEncode($data));
        if(!$this->verifyResponse($res)){
            return false;
        }
        return $res;
    }


    /**
     * 退款订单查询接口
     * @param array $data
     * @return bool|string
     */
    public function refundQuery($data)
    {
        unset($data["mchId"], $data["sign"]);
        $data["mchId"] = ESTConfig::mchId;
        $data["sign"] = $this->getSign($data);

        $res = $this->doPost($this->urlPre.ESTConfig::refundQueryUrl, $this->jsonEncode($data));
        if(!$this->verifyResponse($res)){
            return false;
        }
        return $res;
    }


    /**
     * 分账-子订单查询
     * @param $data
     * @return bool|string
     */
    public function shareAllocationQuery($data)
    {
        $data["sign"] = $this->getSign($data);
        $res = $this->doPost($this->urlPre.ESTConfig::shareAllocationQuery, $this->jsonEncode($data));
        if(!$this->verifyResponse($res)){
            return false;
        }
        return $res;
    }

    /**
     * 分账-子订单录入
     * @param $data
     * @return bool|string
     */
    public function shareAllocationApply($data)
    {
        unset($data["mchId"], $data["sign"]);
        $data["mchId"] = ESTConfig::mchId;
        $data["sign"] = $this->getSign($data);
        $res = $this->doPost($this->urlPre.ESTConfig::shareAllocationApply, $this->jsonEncode($data));
        if(!$this->verifyResponse($res)){
            return false;
        }
        return $res;
    }

    /**
     * 清分  统一退款
     * @param array $data
     * @return bool|string
     */
    public function refundShareCreate($data)
    {
        unset($data["mchId"], $data["sign"]);
        $data["mchId"] = ESTConfig::mchId;
        $data["sign"] = $this->getSign($data);
        $res = $this->doPost($this->urlPre.ESTConfig::refundShareCreateUrl, $this->jsonEncode($data));
        if(!$this->verifyResponse($res)){
            return false;
        }
        return $res;
    }
}