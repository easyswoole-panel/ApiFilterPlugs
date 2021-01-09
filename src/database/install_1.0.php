<?php
use EasySwoole\DDL\Blueprint\Table;
use EasySwoole\DDL\Enum\Character;
use EasySwoole\DDL\Enum\Engine;

try {
    \Siam\Plugs\common\PlugsTableHelper::getInstance()->create("api_filter_setting", function (Table $table) {
        $table->setIfNotExists()->setTableComment('api限流器配置表');   //设置表名称/
        $table->setTableEngine(Engine::MYISAM);                //设置表引擎
        $table->setTableCharset(Character::UTF8MB4_GENERAL_CI);//设置表字符集
        $table->int('set_id', 10)->setColumnComment('id')->setIsAutoIncrement()->setIsPrimaryKey();  //创建user_id设置主键并自动增长
        $table->varchar('key', 50)->setIsNotNull()->setColumnComment('配置key')->setDefaultValue('');
        $table->int('number')->setIsNotNull()->setColumnComment('配置数量')->setDefaultValue(-1);
        $table->datetime('create_time')->setIsNotNull()->setColumnComment('创建时间');
        $table->datetime('update_time')->setIsNotNull()->setColumnComment('更新时间');
    });
    \Siam\ApiFilterPlugs\model\ApiFilterSettingModel::create()->data([
        'key' => \Siam\ApiFilterPlugs\common\AccessContain::API_FILTER_TOTAL,
        'number' => -1
    ])->save();
} catch (Throwable $e) {
    throw $e;
}