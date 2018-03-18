<?php

namespace app\api\model;

use app\api\validate\BaseValidate;
use app\lib\exception\BaseException;
use think\Db;

class Staff extends BaseModel
{

    protected $hidden = ['delete_time', 'update_time'];
    protected $autoWriteTimestamp = true;
    public static function createOne($data)
    {
        Db::startTrans();
        try {
            $need_arr = array(
                'realname','nickname', 'tel'
            );
            $diff_arr = array();
            $data = diffParamFilter($need_arr, $diff_arr, $data);
            $model = new self();
            //管理员联系方式必须唯一
            if ($model->where(array('tel' => $data['tel']))->find()) {
                throw new BaseException(
                    array(
                        'code' => '60010',
                        'msg' => 'tel must be unique! | 管理员联系方式已经存在'
                    )
                );
            }
            $model->allowField(true)->save($data);
            Db::commit();
            return array('code' => 'success');
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            throw $e;
        }
    }

    public static function getAll($where)
    {
        $model = new self();
        //过滤空参数
        $validate = new BaseValidate();
        $where = $validate::emptyFilter($where);
        //名字采用模糊查询
        if (isset($where['realname'])) {
            $where['realname'] = ['like', '%' . $where['realname'] . '%'];
        }
        //昵称采用模糊查询
        if (isset($where['nickname'])) {
            $where['nickname'] = ['like', '%' . $where['nickname'] . '%'];
        }
        //页码信息
        if (isset($where['page']) && isset($where['size'])) {
            $page = $where['page'] . ',' . $where['size'];
        }
        unset($where['page']);
        unset($where['size']);
        $total = $model->where($where)->count();
        if (isset($page)) {
            $model->page($page);
        }
        $res = $model->where($where)->select();
        return array('data' => $res, 'total' => $total);
    }

    public static function getById($id)
    {
        $res = self::where('id', '=', $id)->find();
        if(empty($res)) {
            throw new BaseException(array(
                'msg' => 'no such staff'
            ));
        }
        return $res;
    }

    public static function updateOne($id,$data){
        $model = new self();
        return $model->save($data,['id'=>$id]);
    }

}
