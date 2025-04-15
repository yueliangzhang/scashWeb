<?php
use think\facade\Route;

//定义云
/*Route::get('aws', 'home/ecs/aws');
Route::get('gcp', 'home/ecs/gcp');
Route::get('azure', 'home/ecs/azure');
Route::get('tencent', 'home/ecs/tencent');
Route::get('huawei', 'home/ecs/huawei');*/

//定义云计算分类
Route::get('Computing/:name', 'home/computing/index')->pattern([
    'name' => '[\w\-]+'
]);

Route::get('computing/:name', 'home/computing/index')->pattern([
    'name' => '[\w\-]+'
]);

//定义主服务器分类
Route::get('Dedicated/:name', 'home/stand/index')->pattern([
    'name' => '[\w\-]+'
]);

Route::get('dedicated/:name', 'home/stand/index')->pattern([
    'name' => '[\w\-]+'
]);

//定义产品
Route::get('Cloud/:name', 'home/cloud/class')->pattern([
    'name' => '[\w\-]+'
]);

Route::get('cloud/:name', 'home/cloud/class')->pattern([
    'name' => '[\w\-]+'
]);

//定义产品详细
Route::get('vps/:name', 'home/shop/vps')->pattern([
    'name' => '[\w\-]+'
]);

//定义服务器
Route::get('Server/:name', 'home/server/class')->pattern([
    'name' => '[\w\-]+'
]);

Route::get('server/:name', 'home/server/class')->pattern([
    'name' => '[\w\-]+'
]);

Route::get('stand/:name', 'home/goods/stand')->pattern([
    'name' => '[\w\-]+'
]);

//定位文章分类
Route::get('Solution/:name', 'home/solution/class')->pattern([
    'name' => '[\w\-]+'
]);

Route::get('solution/:name', 'home/solution/class')->pattern([
    'name' => '[\w\-]+'
]);

//文章详细
Route::get('article/:name', 'home/article/index')->pattern([
    'name' => '[\w\-]+'
]);

//定义标签
Route::get('tag/:name', 'home/tag/index')->pattern([
    'name' => '[\w\-]+'
]);