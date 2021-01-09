<?php
/**
 * Created by PhpStorm.
 * User: Siam
 * Date: 2021/1/9
 * Time: 21:06
 */

namespace Siam\ApiFilterPlugs\controller;


use Siam\ApiFilterPlugs\model\ApiFilterSettingModel;
use Siam\Plugs\controller\BasePlugsController;

class ApiFilterController extends BasePlugsController
{
    /**
     * @return bool
     * @throws \EasySwoole\Mysqli\Exception\Exception
     * @throws \EasySwoole\ORM\Exception\Exception
     * @throws \Throwable
     */
    public function add()
    {
        $model = ApiFilterSettingModel::create();
        $has = $model->where([
            'key' =>$this->request()->getRequestParam("key")
        ])->get();
        if ($has) return $this->writeJson(500,[],'该key已经存在 不能新增');
        $model->key = $this->request()->getRequestParam("key");
        $model->number = $this->request()->getRequestParam("number");
        $model->save();
        return $this->writeJson(200,[],'success');
    }

    /**
     * @return bool
     * @throws \EasySwoole\ORM\Exception\Exception
     * @throws \Throwable
     */
    public function  get_list()
    {
        $request = $this->request();
        $list = ApiFilterSettingModel::create()->page($request->getRequestParam('page') ?? 1, $request->getRequestParam('limit') ?? 10)
            ->all();
        $count = ApiFilterSettingModel::create()->count();
        return $this->writeJson(200, [
            'list' => $list,
            'total' => $count
        ]);
    }

    /**
     * @return bool
     * @throws \EasySwoole\ORM\Exception\Exception
     * @throws \Throwable
     */
    public function  delete()
    {
        $setId = $this->request()->getRequestParam('set_id');
        if ($setId){
            ApiFilterSettingModel::create()->destroy([
                'set_id' => $setId
            ]);
        }
        return $this->writeJson(200,[],'success');
    }

    /**
     * @return bool
     * @throws \EasySwoole\Mysqli\Exception\Exception
     * @throws \EasySwoole\ORM\Exception\Exception
     * @throws \Throwable
     */
    public function  edit()
    {
        $setId = $this->request()->getRequestParam('set_id');
        if ($setId){
            $has = ApiFilterSettingModel::create()->get([
                'set_id' => $setId
            ]);
            if (!$has)  return $this->writeJson(500,[],'该key不存在');
            $has->number = $this->request()->getRequestParam('number');
            $has->update();

            return $this->writeJson(200,[],'success');
        }

        return $this->writeJson(201,[],'success');
    }

}