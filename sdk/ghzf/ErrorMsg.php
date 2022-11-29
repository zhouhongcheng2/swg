<?php


trait ErrorMsg
{
    protected $errorMsgArr = [];
    protected $errorCodeArr = [];
    protected $hasError = false;

    protected function setError($msg = '', $code = null)
    {
        $this->errorMsgArr[] = $msg;
        $this->errorCodeArr[] = $code != null ? $code : (count($this->errorMsgArr) - 1);
        $this->hasError = true;
    }

    protected function setErrors($msgArr = [])
    {
        foreach ($msgArr as $code => $msg) {
            $this->setError($msg, $code);
        }
    }

    public function hasError()
    {
        return $this->hasError;
    }

    /**
     * 获取到最后一次的错误信息
     * @return string
     */
    public function getError()
    {
        if (!$this->hasError()) {
            return '';
        }
        return $this->errorMsgArr[count($this->errorMsgArr) - 1];
    }

    /**
     * 获取到最后一次的错误码
     * @return string
     */
    public function getErrorOriginCode()
    {
        if (!$this->hasError()) {
            return '';
        }
        return $this->errorCodeArr[count($this->errorCodeArr) - 1];
    }

    /**
     * 获取全部的错误信息
     * @return array
     */
    public function getErrors()
    {
        return $this->errorMsgArr;
    }

    /**
     * 获取全部的错误code
     * @return array
     */
    public function getErrorOriginCodes()
    {
        return $this->errorCodeArr;
    }

    /**
     * 使用的时候要注意, 设置重复的code会被覆盖
     * 注意 array_combine 的行为
     * 获取全部的msg与Codes
     * @return array
     */
    public function getErrorsAndCodes()
    {
        return array_combine($this->errorCodeArr, $this->errorMsgArr);
    }

    /**
     * 重置错误消息
     */
    public function resetErrors()
    {
        $this->errorMsgArr = [];
        $this->errorCodeArr = [];
        $this->hasError = false;
    }

}