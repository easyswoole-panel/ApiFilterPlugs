<?php

namespace Siam\ApiFilterPlugs\service;


use Siam\ApiFilterPlugs\common\AccessContain;
use Siam\ApiFilterPlugs\model\ApiFilterSettingModel;

class FilterSetting
{
    public static function getAll()
    {
        return ApiFilterSettingModel::create()->all();
    }
}