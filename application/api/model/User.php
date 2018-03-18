<?php

namespace app\api\model;

use app\lib\exception\BaseException;
use think\Db;
use app\api\model\Consume as ConsumeModel;
use app\api\model\UserInfo as UserInfoModel;

class User extends BaseModel
{
    protected $autoWriteTimestamp = true;

//    protected $createTime = ;
    public static function createOne($data)
    {
        Db::startTrans();
        try {
            $need_arr = array(
                'realname', 'tel'
            );
            $diff_arr = array();
            $data = diffParamFilter($need_arr, $diff_arr, $data);
            $model = new User();
            //用户联系方式必须唯一
            if ($model->where(array('tel' => $data['tel']))->find()) {
                throw new BaseException(
                    array(
                        'code' => '60010',
                        'msg' => 'tel must be unique! | 用户联系方式已经存在'
                    )
                );
            }
            $model->allowField(true)->save($data);
            $user_info = isset($data['user_info']) ? $data['user_info'] : array();
            $user_info['user_id'] = $model->id;
            UserInfoModel::createOne($user_info);
            Db::commit();
            return array('code' => 'success');
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            throw $e;
        }
    }

    public function orders()
    {
        return $this->hasMany('Order', 'user_id', 'id');
    }

    public function coupons()
    {
        return $this->belongsToMany('Coupon', 'user_coupon', 'coupon_id');
    }

    public function userInfo()
    {
        return $this->hasOne('UserInfo', 'user_id', 'id');
    }

    public static function getAll($where)
    {
        $model = new User();
        //名字采用模糊查询
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
        if (empty($res)) {
            return array('code' => 'success', 'data' => array(), 'total' => 0, 'msg' => 'empty');
        }
        return array('code' => 'success', 'data' => $res, 'total' => $total);

    }

    /**
     * 用户是否存在
     * 存在返回uid，不存在返回0
     */
    public static function getByOpenID($openid)
    {
        $user = self::where('openid', '=', $openid)->find();
        return $user;
    }

    public static function getUserById($id)
    {
        $res = self::where('id', '=', $id)->find();
        if (empty($res)) {
            throw new BaseException(array(
                'msg' => 'no such user'
            ));
        }
        return $res;
    }

    /**
     * 更新用户资料
     * @param $data
     * @return array
     */
    public static function updateInfo($data)
    {
        if (empty($data['user_id'])) {
            //更新

            $res = self::where(array('user_id' => $data['user_id']))->update($data);
        } else {
            //新增
            $need_arr = array(
                'user_id',
            );
            $fitler = array(
                ''
            );
            $res = self::save($data);
        }
        if ($res) {
            return array('code' => 'success');
        } else {
            return array('code' => 'error');
        }

    }

    public static function updateOne($id, $user)
    {
        //编辑分为两步，一步是基本信息，一步是拓展信息
        Db::startTrans();
        $user_info = $user['user_info'];
        if ($user_info) {
            $res1 = UserInfoModel::updateOne($id, $user_info);
            unset($user['user_info']);
            if ($res1['code'] != 'success') {
                Db::rollback();
                throw new BaseException(array(
                    'msg' => $res1['msg']
                ));
            }
        }
        //基本信息
        $need_arr = array();
        $diff_arr = array(
            'user_id', 'id', 'password', 'delete_time', 'delete_time', 'update_time', 'openid', 'id', 'unionid'
        );
        $data = diffParamFilter($need_arr, $diff_arr, $user);
        $db = new self();
        $db->where(array('id' => $user['id']));
        $db->allowField(true);
        $res2 = $db->update($data);
        if (!$res2) {
            Db::rollback();
            throw new BaseException(array(
                'msg' => $res1['msg']
            ));
        }
        //通过后提交信息
        Db::commit();
        return array('code' => 'success', 'msg' => 'success');
    }

}
