<?php
/**
 * Created by 七月
 * Author: 七月
 * Date: 2017/2/18
 * Time: 15:44
 */

namespace app\lib\exception;

/**
 * 成功返回数据
 */
class SuccessReturn extends BaseException
{
    public $code = 201;
    public $msg = 'ok';
    public $errorCode = 0;
    public $data = array();
}