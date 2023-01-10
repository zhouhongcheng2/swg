<?php
namespace Swg\Composer\bean;

/**
 * 操作日志bean，用于redis存储
 * @author yyl
 */
class OpeareLog
{
    /** @var string 日志标题 */
    public $title;
    /** @var string 日志内容 */
    public $content;
    /** @var string 日志时间 */
    public $time;
    /** @var mixed 设备唯一标示 */
    public $identity;
    /** @var string 操作人 */
    public $operator;
    /** @var int 排序 */
    public $score;

    /**
     * @param string $title log title
     * @param string|array $content log content
     * @param string $identity equipment identity
     * @param string $operator operator
     * @param int|null $time
     */
    public function __construct(string $title,$content,string $identity,string $operator)
    {
        $this->title = $title;
        $this->content= is_array($content)? json_encode($content,true) : $content;
        $this->time = time();
        $this->operator = $operator;
        $this->identity = $identity;

        list($msec, $sec) = explode(' ', microtime());

        $time_micro = (int) ($sec.substr($msec, 2, 6)); // 1491536422147300
        $this->score = $time_micro;
    }
}