<?php
/**
 * @author yyl
 */

namespace Swg\Composer\utility;

/**
 * 运行时间计算
 */
class RunTime
{
    public static $startTime = 0;

    public static function start()
    {
        self::$startTime = microtime();
    }

    public static function end()
    {
        dd(bcsub(microtime(), self::$startTime));
    }
}