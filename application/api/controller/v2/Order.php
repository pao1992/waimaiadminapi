<?php
/**
 * Created by 七月.
 * Author: 七月
 * 微信公号：小楼昨夜又秋风
 * 知乎ID: 七月在夏天
 * Date: 2017/2/22
 * Time: 21:52
 */

namespace app\api\controller\v2;

use app\api\controller\BaseController;
use app\api\model\Order as OrderModel;
use app\api\service\Order as OrderService;
use app\api\service\Token;
use app\api\validate\IDMustBePositiveInt;
use app\api\validate\OrderUpdate;
use app\api\validate\PagingParameter;
use app\lib\exception\BaseException;
use app\lib\exception\OrderException;
use app\lib\exception\SuccessMessage;
use app\api\validate\BaseValidate;
use app\lib\exception\SuccessReturn;
use think\Request;

class Order extends BaseController
{
    protected $beforeActionList = [
        'checkExclusiveScope' => ['only' => 'placeOrder'],
//        'checkPrimaryScope' => ['only' => 'getDetail,getSummaryByUser'],
//        'checkSuperScope' => ['only' => 'delivery,getSummary']
    ];
    

    /**
     * 获取订单详情
     * @param $id
     * @return static
     * @throws OrderException
     * @throws \app\lib\exception\ParameterException
     */
    public function getById($id)
    {
        (new IDMustBePositiveInt())->goCheck();
        $model = new OrderModel();
        $orderDetail = $model::where(['id'=>$id])->find();
        if (!$orderDetail)
        {
            throw new OrderException();
        }
        return new SuccessReturn(
            ['data'=>$orderDetail->hidden(['prepay_id'])]
        );
    }

    /**
     * 根据用户id分页获取订单列表（简要信息）
     * @param int $page
     * @param int $size
     * @return array
     * @throws \app\lib\exception\ParameterException
     */
    public function getSummaryByUser($page = 1, $size = 15)
    {
        (new PagingParameter())->goCheck();
        $uid = Token::getCurrentUid();
        $pagingOrders = OrderModel::getSummaryByUser($uid, $page, $size);
        if ($pagingOrders->isEmpty())
        {
            return [
                'current_page' => $pagingOrders->currentPage(),
                'data' => []
            ];
        }
//        $collection = collection($pagingOrders->items());
//        $data = $collection->hidden(['snap_items', 'snap_address'])
//            ->toArray();
        $data = $pagingOrders->hidden(['snap_items', 'snap_address'])
            ->toArray();
        return [
            'current_page' => $pagingOrders->currentPage(),
            'data' => $data
        ];

    }

    public function getAll(){
        $filter = input('get.');
        //过滤空字段
        $data = BaseValidate::emptyFilter($filter);
        $res = OrderModel::getAll($data);
        return new SuccessReturn(
            array('data'=>$res)
        );
    }
    public function updateOne($id){
        (new IDMustBePositiveInt())->goCheck();
        $data = input('put.');
        $res = OrderModel::updateOne($id,$data);
        if($res){
            return new SuccessMessage();
        }
    }
    public function delivery($id){
        (new IDMustBePositiveInt())->goCheck();
        $order = new OrderService();
        $success = $order->delivery($id);
        if($success){
            return new SuccessMessage();
        }
    }
    public function pay($id){
        OrderModel::save(['pay_status'=>'2'],$id);
    }
}






















