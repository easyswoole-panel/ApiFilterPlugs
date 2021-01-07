<?php
namespace Siam\ApiFilterPlugs;
use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\Component\Process\Manager;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use Siam\ApiFilterPlugs\common\AccessContain;
use Siam\ApiFilterPlugs\common\Event;
use Siam\Plugs\common\utils\PlugsHook;

class PlugsInitialization
{
    public static function init()
    {
        static::initProcess();
        static::registeDefaultEvent();
        static::initOnRequest();
    }

    public static function initProcess()
    {
        AccessContain::getInstance();
        $class = new class('ApiFilter') extends AbstractProcess
        {
            protected function run($arg)
            {
                $this->addTick(1 * 1000, function () {
                    AccessContain::getInstance()->clear();
                });
                // todo 定期从数据库同步配置到table中 1分钟1次
            }
        };
        $processConfig = new \EasySwoole\Component\Process\Config();
        $processConfig->setProcessName('EasySwoolePanel');// 设置进程名称
        $processConfig->setProcessGroup('EasySwoolePanel');// 设置进程组
        $processConfig->setRedirectStdinStdout(false);// 是否重定向标准io
        $processConfig->setPipeType($processConfig::PIPE_TYPE_SOCK_DGRAM);// 设置管道类型
        $processConfig->setEnableCoroutine(true);// 是否自动开启协程
        $processConfig->setMaxExitWaitTime(3);// 最大退出等待时间
        Manager::getInstance()->addProcess(new $class($processConfig));
    }

    private static function initOnRequest()
    {
        PlugsHook::getInstance()->add("ON_REQUEST",  function (Request $request, Response $response)
        {
            $fd = $request->getSwooleRequest()->fd;
            $ip = ServerManager::getInstance()->getSwooleServer()->getClientInfo($fd)['remote_ip'];

            // 全局限流
            $set = AccessContain::getInstance()->getSetting(AccessContain::API_FILTER_TOTAL);
            if ($set >= 0){
                if (AccessContain::getInstance()->access(AccessContain::API_FILTER_TOTAL) > $set) {
                    PlugsHook::getInstance()->hook(Event::API_FILTER_TOTAL_EVENT, $request, $response);
                    return false;
                }
            }

            // ip限流
            $set = AccessContain::getInstance()->getSetting($ip);
            if ($set >= 0) {
                if (AccessContain::getInstance()->access($ip) > $set) {
                    PlugsHook::getInstance()->hook(Event::API_FILTER_IP_EVENT, $request, $response);
                    return false;
                }
            }

            // token限流/分组限流/自定义限流
            // 从hook返回token值和setting值  setting可选 不返回则从table获取
            $token = "";
            if ($token){
                if (AccessContain::getInstance()->access($token) > 3) {
                    ServerManager::getInstance()->getSwooleServer()->close($fd);
                    echo "token限流\n";
                    return false;
                }
            }

            return true;
        });
    }

    private static function registeDefaultEvent()
    {
        PlugsHook::getInstance()->set(Event::API_FILTER_TOTAL_EVENT, function( Request $request, Response $response) {
            $response->write("系统繁忙，请稍后再试。");
            $response->end();
        });

        PlugsHook::getInstance()->set(Event::API_FILTER_IP_EVENT, function( Request $request, Response $response) {
            $response->write("系统繁忙，请稍后再试。");
            $response->end();
        });
    }
}
PlugsInitialization::init();