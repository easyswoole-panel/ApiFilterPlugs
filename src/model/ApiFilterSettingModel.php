<?php
/**
 * Created by PhpStorm.
 * User: Siam
 * Date: 2021/1/9
 * Time: 20:51
 */

namespace Siam\ApiFilterPlugs\model;


use Siam\PLugs\model\BaseModel;

class ApiFilterSettingModel extends BaseModel
{
    protected $tableName = 'api_filter_setting';
    protected $autoTimeStamp = 'datetime';
}