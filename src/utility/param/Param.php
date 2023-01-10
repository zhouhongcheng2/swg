<?php
/**
 * @uthor xxyangyoulin
 */

namespace Swg\Composer\utility\param;

/**
 * 获取所有请求参数
 *
 * 为啥有这个类？当提交的数据类型不是form-data/urlencoded/json的时候，tp框架的input()无法解析！
 * 为啥单独建一个类？是为了方便不是Controller的地方调用
 * Author: yyl
 * datetime 2022/10/28 21:21
 */
class Param
{
    private $params = [];

    /**
     * tp框架支持的的类型
     */
    const ORIGINAL_USABLE_TYPE = [
        'application/json',
        'application/json;charset=UTF-8',
        'multipart/form-data',
        'application/x-www-form-urlencoded'
    ];

    public function __construct()
    {
        //1.如果是tp框架支持的类型，调用tp框架方法
        if (!request()->isPost() ||
            (request()->isPost() && in_array(request()->contentType(), self::ORIGINAL_USABLE_TYPE))) {
            $this->params = input("param.");
            return;
        }

        //2.如果tp不支持，则手动处理
        //使用php://input获取参数的原因：当提交的数据类型不是form-data/urlencoded/json的时候，tp框架的input()无法解析！
        $this->params = json_decode(file_get_contents("php://input"), true);
        //json_decode可能返回null，兼容数据类型，预防使用时没做判断而报错
        if ($this->params == null) $this->params = [];
        //如果获取的数据是数组，则合并上query参数，同名参数时，post数据覆盖get的query数据
        if (is_array($this->params)) $this->params = array_merge(input('get.'), $this->params);
    }

    /**
     * 获取所有参数
     * @return array|mixed
     */
    public function allInput()
    {
        return $this->params;
    }

    /**
     * 获取某个参数值
     * @param string $key 参数名
     * @param mixed $default 默认值
     * @return mixed|null
     */
    public function input(string $key, $default = null)
    {
        if ((isset($this->params[$key]))) {
            if (!empty($this->params[$key]) || $this->params[$key] == '0') {
                if (is_string($this->params[$key])) return trim($this->params[$key]);
                return $this->params[$key];
            }
        }
        return $default;
    }

    /**
     * 获取Bool类型参数
     * @param string $key
     * @param bool|null $default
     * @return bool|null
     */
    public function boolInput(string $key, bool $default = null): ?bool
    {
        if (!isset($this->params[$key])) {
            return $default;
        }
        $param = $this->params[$key];
        if (strtolower($param) === "false") {
            return false;
        }
        return boolval($param);
    }
}