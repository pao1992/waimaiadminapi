<?php

namespace app\api\controller\v2;


use app\api\controller\BaseController;
use app\lib\exception\BaseException;
use app\lib\exception\SuccessMessage;
use app\lib\exception\SuccessReturn;
use app\api\model\System as SystemModel;

class System extends BaseController
{
    protected $beforeActionList = [
        'checkPrimaryScope' => ['only' => 'test']
    ];

    public function getSummary()
    {
        $system = SystemModel::getSummary();
        return new SuccessReturn([
            'data'=>$system
        ]);
    }

    public function update()
    {
        $data = input('put.');
        $model = new SystemModel();
        $res = $model->allowField(true)->save($data,['id'=>1]);
        if(!$res){
            return new BaseException(['msg'=>'没有成功']);
        }
        return new SuccessMessage();
    }
}