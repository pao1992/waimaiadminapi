<?php
/**
 * Created by 七月
 * Author: 七月
 * Date: 2017/2/18
 * Time: 13:47
 */

namespace app\lib\exception;


class UpdateException extends BaseException
{
    public $code = 500;
    public $msg = '编辑失败！';
    public $errorCode = 999;
}