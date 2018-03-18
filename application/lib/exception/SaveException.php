<?php

namespace app\lib\exception;


class SaveException extends BaseException
{
    public $code = 400;
    public $msg = 'saving failed | 保存失败';
    public $errorCode = 10006;
}