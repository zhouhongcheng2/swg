<?php
/**
 * @author yyl
 */

namespace Swg\Composer\exception;

use Throwable;

/**
 * 微信交互异常
 */
class WxException extends \Exception
{
    public function __construct($message, $code = 9, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}