<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\Route;
//获取banner信息
Route::get('api/:version/banner/:id','api/:version.Banner/getBanner');

//获取主题信息

Route::group('api/:version/theme',function(){
//    避免提前匹配
    Route::get('', 'api/:version.Theme/getSimpleList');
    Route::get('/:id', 'api/:version.Theme/getComplexOne');
    Route::post(':t_id/product/:p_id','api/:version.Theme/addThemeProduct');
});

//Route::post('api/:version/product', 'api/:version.Product/createOne');
//Route::delete('api/:version/product/:id', 'api/:version.Product/deleteOne');
Route::get('api/:version/product/by_category/paginate/:id', 'api/:version.Product/getByCategory');
Route::get('api/:version/product/by_category/:id', 'api/:version.Product/getAllInCategory');
Route::get('api/:version/product/:id', 'api/:version.Product/getOne',[],['id'=>'\d+']);
Route::get('api/:version/product/recent/:count', 'api/:version.Product/getRecent');


Route::get('api/:version/category', 'api/:version.Category/getCategory');
// 正则匹配区别id和all，注意d后面的+号，没有+号将只能匹配个位数
//Route::get('api/:version/category/:id', 'api/:version.Category/getCategory',[], ['id'=>'\d+']);
//Route::get('api/:version/category/:id/products', 'api/:version.Category/getCategory',[], ['id'=>'\d+']);
Route::get('api/:version/category/all', 'api/:version.Category/getAllCategories');

//获取Token 需要权限
Route::post('api/:version/token/user', 'api/:version.Token/getToken');

//第三方
Route::post('api/:version/token/app', 'api/:version.Token/getAppToken');

// 验证token是否存在
Route::post('api/:version/token/verify', 'api/:version.Token/verifyToken');

//Address  需要权限
Route::post('api/:version/address', 'api/:version.Address/createOrUpdateAddress');
Route::get('api/:version/address', 'api/:version.Address/getUserAddress');

//Order 需要权限
Route::post('api/:version/order', 'api/:version.Order/placeOrder');
// l路由规则 必须时正整数（正则匹配）
Route::get('api/:version/order/:id', 'api/:version.Order/getDetail',[], ['id'=>'\d+']);
Route::put('api/:version/order/delivery', 'api/:version.Order/delivery');