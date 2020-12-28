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
        rename(__DIR__ . '/../PlugsInitialization.php-bak', __DIR__ . '/../PlugsInitialization.php');
    }
}

// go install
(new Install())->install();
(new PlugsServerHelper())->restart();
