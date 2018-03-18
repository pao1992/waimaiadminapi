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
use app\api\model\RankingSystem as RankingSystemModel;
use app\api\validate\IDMustBePositiveInt;
use app\lib\exception\BaseException;
use app\lib\exception\SuccessMessage;


class RankingSystem extends BaseController
{
    public static function getAll(){
        $res = RankingSystemModel::getAll();
        return $res->toArray();
    }

    /**
     * 根据id号获取等级
     * @param $id
     * @return mixed
     * @throws BaseException
     */
    public function getById($id){
        (new IDMustBePositiveInt())->check($id);
        $res = RankingSystemModel::getById($id);
        if($res['code'] == 'success'){
            return $res['data'];
        }else{
            throw new BaseException(
                array('msg'=>$res['msg'])
            );
        }
    }
    public function createOne(){
        $data = input('post.');
        $res = RankingSystemModel::createOne($data);
        if($res['code'] == 'success'){
            return new SuccessMessage();
        }else{
            return new BaseException(array(
                'msg'=>$res['msg']
            ));
        }
    }
    public function updateOne($id){
        (new IDMustBePositiveInt())->check($id);
        $data = input('put.');
//        $need_arr = array();
//        $diff_arr = array('id');
//        $data = diffParamFilter($need_arr,$diff_arr,$data);
//        print_r($data);die;
        $res = RankingSystemModel::updateOne($id,$data);
        if($res['code'] == 'success'){
            return new SuccessMessage();
        }else{
            return new BaseException(array(
                'msg'=>$res['msg']
            ));
        }
    }
    public function deleteOne($id){
        (new IDMustBePositiveInt())->check($id);
        $res = RankingSystemModel::deleteOne($id);
        if($res['code'] == 'success'){
            return new SuccessMessage();
        }else{
            return new BaseException(array(
                'msg'=>$res['msg']
            ));
        }
    }

}