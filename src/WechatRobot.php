<?php

namespace Swg\Composer;

/** 企业微信机器人发送 */
class WechatRobot
{
    /** @var string 信息部内部异常反馈群 */
    const ROBOT_DEPARTMENT = 'https://qyapi.weixin.qq.com/cgi-bin/webhook/send?key=fc1ac844-de98-447c-a95f-8f9fb25a8b7e';

    /** @var string APP自动事务处理异常通知群 */
    const ROBOT_APP_EXCEPTION = 'https://qyapi.weixin.qq.com/cgi-bin/webhook/send?key=f16a523d-36a0-499c-8eec-916394b916d5';

    /** @var string 支付异常通知群 */
    const ROBOT_PAY_EXCEPTION = 'https://qyapi.weixin.qq.com/cgi-bin/webhook/send?key=a2da61e2-782f-4e8b-80ce-019ee8c03226';

    /** @var string 快递通知群 */
    const ROBOT_EXPRESS_EXCEPTION = 'https://qyapi.weixin.qq.com/cgi-bin/webhook/send?key=5e3c6ca2-e34f-49a2-b87b-c6c1bbda76ca';


    /**
     * Author: zhouhongcheng
     * datetime 2022/11/1 16:44
     * @param array $exception_data 异常参数 [{"title":"标题","remark":"异常描述"}]
     * @param string $robot_url 机器人地址
     * @param string $title 异常标题
     * @return bool
     */
    public static function sendWechatRobotMsg(array $exception_data, string $title = '异常警告', string $robot_url = self::ROBOT_DEPARTMENT): bool
    {
        if (!$exception_data) return false;
        $content = '';
        foreach ($exception_data as $val) {
            $content .= '<font color="#808080">' . $val['title'] . '：</font><font color="#0386da">' . $val['remark'] . "</font>\n";
        }
        $data = [];
        $data['msgtype'] = 'markdown';
        $data['markdown']['content'] = "<font color=\"#ef0000\">$title</font>\n" . $content .
            "<font color=\"#808080\">推送时间：</font><font color=\"#0386da\">" . date("Y-m-d H:i:s") . "</font>";
        //推送机器人
        Common::curlPost($robot_url, json_encode($data));
        return true;
    }
}