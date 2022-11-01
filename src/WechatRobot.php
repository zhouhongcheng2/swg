<?php
namespace Swg\Composer;

class WechatRobot
{
    /** string 信息部内部异常反馈群 */
    protected $ROBOT_DEPARTMENT = 'https://qyapi.weixin.qq.com/cgi-bin/webhook/send?key=fc1ac844-de98-447c-a95f-8f9fb25a8b7e';

    /** string APP自动事务处理异常通知群 */
    protected $ROBOT_APP_EXCEPTION = 'https://qyapi.weixin.qq.com/cgi-bin/webhook/send?key=f16a523d-36a0-499c-8eec-916394b916d5';

    public function sendWechatRobotMsg(){
        return $this->ROBOT_APP_EXCEPTION;
    }
}