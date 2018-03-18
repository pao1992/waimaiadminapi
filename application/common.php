<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
use app\lib\exception\ParameterException;
// 应用公共文件
/**
 * 参数整理
 * @param $need_arr 必要的参数
 * @param $diff_arr 排除的参数标准
 * @param $param
 * @return array
 */
function diffParamFilter($need_arr, $diff_arr, $param)
{
    //必要参数不能缺失
    foreach ($need_arr as $k => $v) {
        if (!array_key_exists($v, $param)) {
            throw new ParameterException(
                array(
                    'msg' => $v . ' is missing',
                )
            );
        }
    }
    $data = $param;
    if(!empty($diff_arr)){
        //过滤不需要的参数
        foreach ($diff_arr as $k => $v) {
            if (array_key_exists($v, $param)) {
                unset($data[$v]);
            }
        }
    }

    return $data;
}
