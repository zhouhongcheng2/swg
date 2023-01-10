<?php
/**
 * @uthor xxyangyoulin
 */


namespace Swg\Composer\utility\param\facade;

use think\Facade;

/**
 * 获取所有请求参数
 * 为啥有这个类？当提交的数据类型不是form-data/urlencoded/json的时候，tp框架的input()无法解析！
 * 为啥单独建一个类？是为了方便不是Controller的地方调用
 *
 * @see \Swg\Composer\utility\param\Param
 * @method static array|mixed allInput() 获取全部参数
 * @method static mixed input(string $name, $default = null) 获取某个参数
 * @method static null|bool boolInput(string $name, $default = null) 获取真实bool类型参数
 */
class Param extends Facade
{
    protected static function getFacadeClass()
    {
        return 'Swg\Composer\utility\param\Param';
    }
}