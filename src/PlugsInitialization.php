<?php
namespace IpLimiterPlugs\IpLimiter;
use EasySwoole\Component\Di;
use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\Component\Process\Manager;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use Siam\Plugs\common\PlugsServerHelper;
use Siam\Plugs\common\utils\PlugsHook;

class PlugsInitialization
{
    public static function init()
    {
        // 开启IP限流
        $class = new class('IpAccessCount') extends AbstractProcess
        {
            protected function run($arg)
            {
                $this->addTick(10 * 1000, function () {
                    /**
                     * 正常用户不会有一秒超过6次的api请求
                     * 做列表记录并清空
                     */
                    $list = IpLists::getInstance()->accessList(30);
                    var_dump($list);
                    IpLists::getInstance()->clear();
                });
            }
        };
        $processConfig = new \EasySwoole\Component\Process\Config();
        $processConfig->setProcessName('IP_LIMITER');// 设置进程名称
        $processConfig->setProcessGroup('IP_LIMITER');// 设置进程组
        $processConfig->setArg([]);// 传参
        $processConfig->setRedirectStdinStdout(false);// 是否重定向标准io
        $processConfig->setPipeType($processConfig::PIPE_TYPE_SOCK_DGRAM);// 设置管道类型
        $processConfig->setEnableCoroutine(true);// 是否自动开启协程
        $processConfig->setMaxExitWaitTime(3);// 最大退出等待时间
        Manager::getInstance()->addProcess(new $class($processConfig));

        $onRequestHook = Di::getInstance()->get('HTTP_GLOBAL_ON_REQUEST');
        if ($onRequestHook) {
            Di::getInstance()->set('HTTP_GLOBAL_ON_REQUEST', function (Request $request, Response $response) use ($onRequestHook) {

                if (!($onRequestHook)($request, $response)) {
                    return false;
                }

                $fd = $request->getSwooleRequest()->fd;
                $ip = ServerManager::getInstance()->getSwooleServer()->getClientInfo($fd)['remote_ip'];

                // 如果当前周期的访问频率已经超过设置的值，则拦截
                // 测试的时候可以将30改小，比如3
                if (IpLists::getInstance()->access($ip) > 3) {
                    /**
                     * 直接强制关闭连接
                     */
                    ServerManager::getInstance()->getSwooleServer()->close($fd);
                    // 调试输出 可以做逻辑处理
                    echo '被拦截' . PHP_EOL;
                    return false;
                }
                // 调试输出 可以做逻辑处理
                echo '正常访问' . PHP_EOL;
                return true;
            });
        } else {
            Di::getInstance()->set('HTTP_GLOBAL_ON_REQUEST', function (Request $request, Response $response) {
                $fd = $request->getSwooleRequest()->fd;
                $ip = ServerManager::getInstance()->getSwooleServer()->getClientInfo($fd)['remote_ip'];

                // 如果当前周期的访问频率已经超过设置的值，则拦截
                // 测试的时候可以将30改小，比如3
                if (IpLists::getInstance()->access($ip) > 3) {
                    /**
                     * 直接强制关闭连接
                     */
                    ServerManager::getInstance()->getSwooleServer()->close($fd);
                    // 调试输出 可以做逻辑处理
                    echo '被拦截' . PHP_EOL;
                    return false;
                }
                // 调试输出 可以做逻辑处理
                echo '正常访问' . PHP_EOL;
                return true;
            });
        }
    }
}
PlugsInitialization::init();