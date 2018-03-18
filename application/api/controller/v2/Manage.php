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
use app\api\model\Order as OrderModel;
use app\lib\exception\SuccessReturn;
use think\Db;

class Manage extends BaseController
{
    public function getDailyData(){
        $model = new OrderModel;
        $amount = $model->where(['pay_status'=>'2','date'=>date('Y-n-j')])->sum('total_price');
        $amount = $amount?$amount:0;
        $count = $model->where(['pay_status'=>'2','date'=>date('Y-n-j')])->count();
        return new SuccessReturn([
            'data'=>['daily_sale'=>$amount,'daily_order_num'=>$count]
        ]);
    }

}