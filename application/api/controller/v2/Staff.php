<?php
namespace app\api\controller\v2;


use app\api\controller\BaseController;
use app\api\model\Staff as StaffModel;
use app\api\validate\IDMustBePositiveInt;
use app\lib\exception\SuccessMessage;
use app\lib\exception\BaseException;
use app\lib\exception\SuccessReturn;

class Staff extends BaseController
{
    /**
     * 创建新的记录
     * @param $param
     * @return SuccessMessage
     * @throws BaseException
     */
    Public function createOne()
    {
        $res = StaffModel::createOne(input('post.'));
        return new SuccessMessage();
    }

    public function getAll()
    {
        $res = StaffModel::getAll(input('get.'),true);
        return new SuccessReturn(
            array('data'=>$res)
        );
    }

    public function getById($id)
    {
        (new IDMustBePositiveInt())->check($id);
        $res = StaffModel::getById($id);
        return new SuccessReturn(
            array('data' => $res)
        );
    }
    public function updateOne($id){
        (new IDMustBePositiveInt())->check($id);
        $data = input('put.');
        $res = StaffModel::updateOne($id,$data);
        return new SuccessMessage();
    }
}