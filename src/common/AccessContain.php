<?php
/**
 * Ip访问次数统计
 * User: XueSi
 * Email: <1592328848@qq.com>
 * Date: 2020/12/24
 * Time: 0:29
 */

namespace Siam\ApiFilterPlugs\common;

use EasySwoole\Component\Singleton;
use EasySwoole\Component\TableManager;
use Swoole\Table;

class AccessContain
{
    use Singleton;

    /** @var Table */
    protected $table;

    const API_FILTER_TOTAL = "API_FILTER_TOTAL";

    public function __construct()
    {
        TableManager::getInstance()->add('ipList', [
            'filter_key'     => [
                'type' => Table::TYPE_STRING,
                'size' => 16,
            ],
            'count'          => [
                'type' => Table::TYPE_INT,
                'size' => 8,
            ],
            'setting'        => [
                'type' => Table::TYPE_INT,
                'size' => 8,
            ],// -1为不限制 0为黑名单 整数为限制数
            'lastAccessTime' => [
                'type' => Table::TYPE_INT,
                'size' => 8,
            ],
        ], 1024 * 128);
        $this->table = TableManager::getInstance()->get('ipList');
    }

    function getSetting(string $filter_key)
    {
        return $this->getAuto($filter_key)['setting'];
    }

    function access(string $filter_key): int
    {
        return $this->getAuto($filter_key)['count'];
    }

    function getAuto($filter_key)
    {
        $key  = substr(md5($filter_key), 8, 16);
        $info = $this->table->get($key);

        if ($info) {
            $this->table->set($key, [
                'lastAccessTime' => time(),
                'count'          => $info['count'] + 1,
                'setting'        => $info['setting'],
            ]);
        } else {
            $this->table->set($key, [
                'filter_key'     => $filter_key,
                'lastAccessTime' => time(),
                'count'          => 1,
                'setting'        => -1,
            ]);
        }
        return !!$info ? $info : $this->table->get($key);
    }

    function clear()
    {
        foreach ($this->table as $key => $item) {
            $this->table->del($key);
        }
    }

    function accessList($count = 10): array
    {
        $ret = [];
        foreach ($this->table as $key => $item) {
            if ($item['count'] >= $count) {
                $ret[] = $item;
            }
        }
        return $ret;
    }

}