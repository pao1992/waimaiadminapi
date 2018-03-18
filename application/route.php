<?php
/**
 * 路由注册
 *
 * 以下代码为了尽量简单，没有使用路由分组
 * 实际上，使用路由分组可以简化定义
 * 并在一定程度上提高路由匹配的效率
 */

// 写完代码后对着路由表看，能否不看注释就知道这个接口的意义
use think\Route;

//Miss 404
//Miss 路由开启后，默认的普通模式也将无法访问
//Route::miss('api/v1.Miss/miss');

//Banner
Route::group('api/:version/banner', function () {
    Route::get('', 'api/:version.Banner/getAll');
    Route::post('', 'api/:version.Banner/createOne');

    Route::post(':/id', 'api/:version.Banner/uploadImage',[],['id'=>'\d+']);
    Route::delete(':id', 'api/:version.Banner/deleteOne',[],['id'=>'\d+']);
    Route::get('/:id', 'api/:version.Banner/getOne');
    Route::put('/:id', 'api/:version.Banner/updateOne');

    Route::post('/upload_img', 'api/:version.Banner/uploadImage');
    //item
    Route::post('/item', 'api/:version.Banner/createItem');
    Route::post('/item/resort', 'api/:version.Banner/ItemReSort');
    Route::delete('/item/:id', 'api/:version.Banner/deleteItem',[],['id'=>'\d+']);
    Route::put('/item/:id', 'api/:version.Banner/updateItem',[],['id'=>'\d+']);
});


//Theme
// 如果要使用分组路由，建议使用闭包的方式，数组的方式不允许有同名的key
//Route::group('api/:version/theme',[
//    '' => ['api/:version.Theme/getThemes'],
//    ':t_id/product/:p_id' => ['api/:version.Theme/addThemeProduct'],
//    ':t_id/product/:p_id' => ['api/:version.Theme/addThemeProduct']
//]);

Route::group('api/:version/theme', function () {
    Route::get('', 'api/:version.Theme/getAll');
    Route::get('/:id', 'api/:version.Theme/getOne');
    Route::put('/:id', 'api/:version.Theme/updateOne');
    Route::post('', 'api/:version.Theme/createOne');
    Route::delete(':id', 'api/:version.Theme/deleteOne');
    Route::get('/:id/product', 'api/:version.Theme/getProductsByTheme');
    Route::post(':t_id/product/:p_ids', 'api/:version.Theme/addThemeProduct');
    Route::delete(':t_id/product/:p_ids', 'api/:version.Theme/deleteThemeProduct');
});

//Product
Route::group('api/:version/product', function () {
    Route::post('', 'api/:version.Product/createOne');
    Route::get('/:id', 'api/:version.Product/getById', [], ['id' => '\d+']);
    Route::put('/:id', 'api/:version.Product/updateOne', [], ['id' => '\d+']);
    Route::put('/batch', 'api/:version.Product/batchUpdate');
    Route::delete('/:id', 'api/:version.Product/deleteOne');
    Route::get('/by_category/:id', 'api/:version.Product/getByCategory', [], ['id' => '\d+']);
    Route::get('/recent', 'api/:version.Product/getRecent');
    Route::get('', 'api/:version.Product/getAll');
});
//coupon
Route::post('api/:version/coupon', 'api/:version.Coupon/createOne');
Route::get('api/:version/coupon/:id', 'api/:version.Coupon/getOne', [], ['id' => '\d+']);
Route::get('api/:version/coupon/all', 'api/:version.Coupon/getAllCoupons', [], ['id' => '\d+']);
Route::delete('api/:version/coupon/:id', 'api/:version.Coupon/deleteOne', [], ['id' => '\d+']);
Route::get('api/:version/coupon/by_user', 'api/:version.Coupon/getCouponByUser', [], ['id' => '\d+']);
Route::get('api/:version/coupon/by_date', 'api/:version.Coupon/getCouponsByDate', [], ['id' => '\d+']);
//userCoupon
Route::get('api/:version/userCoupon', 'api/:version.UserCoupon/getAllUserCoupons');
Route::delete('api/:version/userCoupon/:id', 'api/:version.UserCoupon/deleteOne', [], ['id' => '\d+']);

//user
Route::group('api/:version/user', function () {
    Route::get('', 'api/:version.User/getAll');
    Route::get('/:id', 'api/:version.User/getUserById', [], ['id' => '\d+']);
    Route::put('/:id', 'api/:version.User/updateOne', [], ['id' => '\d+']);
    Route::post('', 'api/:version.User/createOne');

});
Route::post('api/:version/user/by_card/:id', 'api/:version.User/getUserByCard', [], ['id' => '\d+']);

//userinfo
Route::group('api/:version/userinfo', function () {
    Route::get('/:id', 'api/:version.User/getInfo', [], ['id' => '\d+']);
    Route::put('/:id', 'api/:version.Category/updateCategory', [], ['id' => '\d+']);
    Route::put('/:id', 'api/:version.Category/updateCategory', [], ['id' => '\d+']);
});

//Category
Route::get('api/:version/category', 'api/:version.Category/getAll');
Route::post('api/:version/category', 'api/:version.Category/createOne');
// 正则匹配区别id和all，注意d后面的+号，没有+号将只能匹配个位数
Route::group('api/:version/category/:id', function () {
    Route::put('', 'api/:version.Category/updateOne');
    Route::get('', 'api/:version.Category/getById');
    Route::delete('', 'api/:version.Category/deleteOne');
}, ['id' => '\d+']);


//Token
Route::post('api/:version/token/user', 'api/:version.Token/getToken');

Route::post('api/:version/token/app', 'api/:version.Token/getAppToken');
Route::post('api/:version/token/verify', 'api/:version.Token/verifyToken');

//Address
Route::post('api/:version/address', 'api/:version.Address/createOrUpdateAddress');
Route::get('api/:version/address', 'api/:version.Address/getUserAddress');

//Order
//Route::post('api/:version/order', 'api/:version.Order/placeOrder');

Route::group('api/:version/order', function () {
    Route::get('/pay/:id', 'api/:version.Order/pay');//临时支付接口，正式项目请务必傻逼删除

    Route::get('', 'api/:version.Order/getAll');
    Route::get('/:id', 'api/:version.Order/getById', [], ['id' => '\d+']);
    Route::put('/:id', 'api/:version.Order/updateOne', [], ['id' => '\d+']);
});
//Route::put('api/:version/order/delivery', 'api/:version.Order/delivery');


//不想把所有查询都写在一起，所以增加by_user，很好的REST与RESTFul的区别
Route::get('api/:version/order/by_user', 'api/:version.Order/getSummaryByUser');

//Pay
Route::post('api/:version/pay/pre_order', 'api/:version.Pay/getPreOrder');
Route::post('api/:version/pay/notify', 'api/:version.Pay/receiveNotify');
Route::post('api/:version/pay/re_notify', 'api/:version.Pay/redirectNotify');
Route::post('api/:version/pay/concurrency', 'api/:version.Pay/notifyConcurrency');

//Message
Route::post('api/:version/message/delivery', 'api/:version.Message/sendDeliveryMsg');
//system
Route::get('api/:version/system', 'api/:version.System/getSummary');
Route::put('api/:version/system', 'api/:version.System/update');

//文件上传
Route::post('api/:version/upload', 'api/:version.Upload/index');
Route::post('api/:version/upload/product_img', 'api/:version.Upload/productImgUpload');
Route::post('api/:version/upload/theme_topic_img', 'api/:version.Upload/themeTopicImgUpload');


//dispatch_range
Route::group('api/:version/dispatch_range', function () {
    Route::get('', 'api/:version.Dispatch/getAllRange');
    Route::get('/:id', 'api/:version.Dispatch/getRangeById', [], ['id' => '\d+']);
    Route::post('', 'api/:version.Dispatch/createRange', [], ['id' => '\d+']);
    Route::put('/:id', 'api/:version.Dispatch/updateRange', [], ['id' => '\d+']);
    Route::delete('/:id', 'api/:version.Dispatch/deleteRange', [], ['id' => '\d+']);
});
//dispatch_point
Route::group('api/:version/dispatch_point', function () {
    Route::get('', 'api/:version.Dispatch/getPoint');
    Route::get('/:id', 'api/:version.Dispatch/getPointById');
    Route::post('/:id', 'api/:version.Dispatch/createPoint');
    Route::put('/:id', 'api/:version.Dispatch/updatePoint');
    Route::delete('/:id', 'api/:version.Dispatch/deletePoint');
});

//statistic统计
Route::group('api/:version/statistic',function(){
    Route::get('/sales_volume_bar', 'api/:version.Statistic/salesVolumeBar');
    Route::get('/products_pie', 'api/:version.Statistic/productsPie');
});

//manage
Route::group('api/:version/manage',function(){
    Route::get('/daily_data', 'api/:version.Manage/getDailyData');
});