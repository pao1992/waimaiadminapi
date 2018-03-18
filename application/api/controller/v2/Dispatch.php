<?php
/**
 * Created by 七月.
 * Author: 七月
 * 微信公号：小楼昨夜又秋风
 * 知乎ID: 七月在夏天
 * Date: 2017/2/19
 * Time: 11:28
 */

namespace app\api\controller\v2;


use app\api\controller\BaseController;
use app\api\model\DispatchRange as DispatchRangeModel;
use app\lib\exception\SuccessMessage;
use app\lib\exception\BaseException;
use app\api\validate\IDMustBePositiveInt;
use app\api\validate\BaseValidate;
use app\lib\exception\SuccessReturn;
use app\lib\exception\UpdateException;

class DisPatch extends BaseController
{
    public function getAllRange(){
        $where = input('get.');
       $res = DispatchRangeModel::getAll($where);
       return new SuccessReturn([
           'data'=>$res
       ]);
    }
    Public function createRange()
    {
        $data = input('post.');
//        echo 'dsfdsdfdsf';die;
//        print_r($data);
        $res = DispatchRangeModel::createOne($data);
        return new SuccessMessage();
    }

    Public function updateRange($id)
    {
        (new IDMustBePositiveInt())->check($id);
        $data = input('put.');
        unset($data['id']);
        $res = DispatchRangeModel::updateOne($id,$data);
        if(!$res){
            return new UpdateException();
        }
        return new SuccessMessage();
    }

    public function deleteRange($id)
    {
        (new IDMustBePositiveInt())->check($id);
        $res = DispatchRangeModel::deleteOne($id);
        if(!$res){
            throw New BaseException();
        }
        return new SuccessMessage();
    }

    public function getById($id)
    {
        (new IDMustBePositiveInt())->check($id);
        $res = DispatchModel::getById($id);
        return $res;
    }
    public function getAll()
    {
        $filter = input('get.');
        $res = DispatchModel::getAll($filter);
        return $res;
    }
    public function getInstances($id){
        (new IDMustBePositiveInt())->check($id);
        $filter = input('get.');
        //过滤空字段
        $data = BaseValidate::emptyFilter($filter);

        $res = UserDispatchModel::getInstances($id,$data);
        return $res;
    }
    public function useInstance($id){
        (new IDMustBePositiveInt())->check($id);
        $res = UserDispatchModel::deleteOne($id);
        if ($res) {
            return new SuccessMessage([
                'code' => 204
            ]);
        } else {
            throw new BaseException();
        }
    }


}