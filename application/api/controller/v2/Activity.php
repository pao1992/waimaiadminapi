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
use app\api\model\Activity as ActivityModel;
use app\api\model\UserActivity as UserActivityModel;
use app\lib\exception\SuccessMessage;
use app\lib\exception\BaseException;
use app\api\validate\IDMustBePositiveInt;
use app\api\validate\BaseValidate;

class Activity extends BaseController
{
    Public function createOne()
    {
//        $validate = new CouponNew();
//        $validate->goCheck();
        // 根据规则取字段是很有必要的，防止恶意更新非客户端字段
//        $data = $validate->getDataByRule(input('post.'));
        $data = input('post.');
        $res = ActivityModel::createOne($data);
        if (!$res) {
            throw new BaseException();
        }
        return new SuccessMessage();
    }

    Public function updateOne($id)
    {
        (new IDMustBePositiveInt())->check($id);
//        $validate = new CouponNew();
//        $validate->goCheck();
        // 根据规则取字段是很有必要的，防止恶意更新非客户端字段
//        $data = $validate->getDataByRule(input('post.'));
        $data = input('put.');
        unset($data['id']);
        $res = ActivityModel::updateOne($id,$data);
        if (!$res) {
            throw new BaseException();
        }
        return new SuccessMessage();
    }

    public function deleteOne($id)
    {
        (new IDMustBePositiveInt())->check($id);
        $res = ActivityModel::deleteOne($id);
        if ($res) {
            return new SuccessMessage([
                'code' => 204
            ]);
        } else {
            throw new BaseException();
        }
    }

    public function getActivityById($id)
    {
        (new IDMustBePositiveInt())->check($id);
        $res = ActivityModel::getActivityById($id);
        return $res;
    }

    public function getAllActivities()
    {
        $filter = input('get.');
        $res = ActivityModel::getAllActivities($filter);
        return $res;
    }
    public function getInstances($id){
        (new IDMustBePositiveInt())->check($id);
        $filter = input('get.');
        //过滤空字段
        $data = BaseValidate::emptyFilter($filter);

        $res = UserActivityModel::getInstances($id,$data);
        return $res;
    }
    public function useInstance($id){
        (new IDMustBePositiveInt())->check($id);
        $res = UserActivityModel::deleteOne($id);
        if ($res) {
            return new SuccessMessage([
                'code' => 204
            ]);
        } else {
            throw new BaseException();
        }
    }


}