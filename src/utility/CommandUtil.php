<?php
/**
 * @author yyl
 */

namespace Swg\Composer\utility;

/**
 * 命令行工具
 */
class CommandUtil
{
    /**
     * 判断当前是否是命令行环境
     */
    public static function isCommand(): bool
    {
        if (php_sapi_name() == "cli") {
            return true;
        } else {
            return false;
        }
    }
}