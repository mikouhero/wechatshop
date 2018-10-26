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
//Route::get('api/:version/product/by_category/paginate/:id', 'api/:version.Product/getByCategory');
Route::get('api/:version/product/by_category/:id', 'api/:version.Product/getAllInCategory');
Route::get('api/:version/product/:id', 'api/:version.Product/getOne',[],['id'=>'\d+']);
Route::get('api/:version/product/recent/:count', 'api/:version.Product/getRecent');