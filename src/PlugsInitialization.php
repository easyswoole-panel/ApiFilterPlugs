<?php
namespace IpLimiterPlugs\IpLimiter;

use Siam\Plugs\common\PlugsServerHelper;
use Siam\Plugs\common\utils\PlugsHook;

class PlugsInitialization
{
    public static function init()
    {
        PlugsHook::getInstance()->add('API_LIMIT_TEST', function () {
            var_dump('i am test');
        });
        PlugsHook::getInstance()->hook('API_LIMIT_TEST');
    }
}
PlugsInitialization::init();
(new PlugsServerHelper())->restart();