<?php
/**
 * Created by 七月
 * User: 七月
 * Date: 2017/2/15
 * Time: 13:40
 */

namespace app\api\controller\v2;


use app\api\controller\BaseController;
use app\api\validate\IDMustBePositiveInt;
use app\api\model\User as UserModel;
use app\api\validate\BaseValidate;
use app\lib\exception\BaseException;
use app\lib\exception\SuccessMessage;
use app\lib\exception\SuccessReturn;
use think\Db;
use app\api\model\Consume as ConsumeModel;

class User extends BaseController
{
    public function createOne()
    {
        $res = UserModel::createOne(input('post.'));
        if ($res['code'] == 'success') {
            return new SuccessMessage();
        }
    }

    public function getUserById($id)
    {
        (new IDMustBePositiveInt())->check($id);
        $res = UserModel::getUserById($id);
        return new SuccessReturn(
            array('data' => $res)
        );
    }
    public function getAll()
    {
        $filter = input('get.');
        //过滤空字段
        $data = BaseValidate::emptyFilter($filter);
        $res = UserModel::getAll($data);
        return new SuccessReturn(
            array(
                'data' => array(
                    'data' => $res['data'],
                    'total' => $res['total']
                )
            )
        );
    }

    /**
     * 获取用户信息
     * @param $id
     */
    public function getInfo($id)
    {
        $res = UserModel::getInfo($id);
    }

    /**
     * 编辑用户信息
     * @param $id
     */
    public function updateOne($id)
    {
        (new IDMustBePositiveInt())->check($id);
        $user = input('put.');
        $res = UserModel::updateOne($id, $user);
        if ($res['code'] = 'success') {
            $return = new SuccessMessage(
                array(
                    'msg' => 'update success'
                )
            );
        } else {
            $return = new BaseException(
                array(
                    'msg' => $res['msg']
                )
            );
        }
        echo json_encode($return);
    }


}