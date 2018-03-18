<?php

namespace app\api\model;

class UserInfo extends BaseModel
{
    protected function getFavoriteColorAttr($value)
    {
        if ($value) {
            return explode(',', $value);
        }
        return array();
    }

    protected function setFavoriteColorAttr($value)
    {
        if ($value) {
            return implode(',', $value);
        }
    }

    protected function getTabooAttr($value)
    {
        if ($value) {
            return explode(',', $value);
        }
        return array();
    }

    protected function setTabooAttr($value)
    {
        if ($value) {
            return implode(',', $value);
        }
    }

    public function rankingSystem()
    {
        return $this->hasOne('ranking_system', 'id', 'ranking')->field('id,name')->bind(['ranking_name' => 'name']);
    }
    public static function createOne($data){
        $model = new self();
        return $model->allowField(true)->create($data);
    }
    /**
     * 更新
     * @param $id
     * @param $data
     * @return array
     */
    public static function updateOne($id, $data)
    {
        $diff_arr = array(
            'id', 'user_id'
        );
        $data = diffParamFilter(array(), $diff_arr, $data);
        $db = new self();
        $res = $db->allowField(true)->save($data,['user_id'=>$id]);
        return array('code' => 'success', 'msg' => 'success');

    }
}
