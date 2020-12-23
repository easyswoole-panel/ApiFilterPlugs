<?php
/**
 * IP 限流器插件
 * IpLimiterPlugs
 * version 1.0
 * User: XueSiLf
 * Email: <1592328848@qq.com>
 * Date: 2020/12/23
 * Time: 23:43
 */

namespace IpLimiterPlugs\IpLimiter\database;

use EasySwoole\Component\Di;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\Http\Request;
use Siam\Plugs\common\PlugsBasicHelper;
use Siam\Plugs\common\PlugsServerHelper;
use Siam\Plugs\common\utils\PlugsHook;
use Swoole\Http\Response;

/**
 * 安装脚本
 * Class Install
 * @package DatabaseManage\database
 */
class Install
{
    /**
     * 安装流程
     * @return bool
     */
    public function install()
    {
        // 使用基础包提供的助手类建表
        PlugsHook::getInstance()->add('IP_LIMITER', function () {
            var_dump('233333333333333');

//            /** @var callable $oldOnRequestCallback */
//            $oldOnRequestCallback = Di::getInstance()->get('HTTP_GLOBAL_ON_REQUEST');
//
//            Di::getInstance()->set('HTTP_GLOBAL_ON_REQUEST', function (Request $request, Response $response) use ($oldOnRequestCallback) {
//
//                $oldOnRequestCallback();
//
//                file_put_contents('1.log', 1111);
//
//                $fd = $request->getSwooleRequest()->fd;
//                $ip = ServerManager::getInstance()->getSwooleServer()->getClientInfo($fd)['remote_ip'];
//
//                // 如果当前周期的访问频率已经超过设置的值，则拦截
//                // 测试的时候可以将30改小，比如3
//                if (IpLimiterPlugs\IpLimiter\IpLists::getInstance()->access($ip) > 2) {
//                    /**
//                     * 直接强制关闭连接
//                     */
//                    ServerManager::getInstance()->getSwooleServer()->close($fd);
//                    // 调试输出 可以做逻辑处理
//                    echo '被拦截' . PHP_EOL;
//                    return false;
//                }
//                // 调试输出 可以做逻辑处理
//                echo '正常访问' . PHP_EOL;
//            });
        });
        PlugsHook::getInstance()->hook('IP_LIMITER');
    }
}

// go install
(new Install())->install();
