<?php
use think\Route;

//登录需要保证用户信息的安全性所以使用post
Route::post('/sys/login','sys/Common/login');
Route::post('/sys/logout','sys/Common/logout');
Route::get('/sys/test','sys/auth.Test2/index');
Route::post('/sys/register','sys/Common/register');

Route::group('sys/user',function(){
    // 获取用户列表
    Route::get('/list', 'sys/auth.User/list');
    // 修改密码
    Route::post('/password', 'sys/auth.User/password');
    // 获取当前登录的用户信息
    Route::get('/info', 'sys/auth.User/info');
    // 根据id获取用户信息
    Route::get('/info/:id', 'sys/auth.User/info');
    // 添加用户
    Route::post('/save', 'sys/auth.User/save');
    // 修改用户
    Route::post('/update', 'sys/auth.User/update');
    // 删除用户
    Route::post('/delete', 'sys/auth.User/delete');
    Route::get('/test', 'sys/auth.User/test');
});

Route::group('sys/menu',function(){
    // 获取导航菜单列表 / 权限
    Route::get('/nav', 'sys/auth.Menu/nav');
    // 获取菜单列表
    Route::get('/list', 'sys/auth.Menu/list');
    // 获取上级菜单
    Route::get('/select', 'sys/auth.Menu/select');
    // 获取菜单信息
    Route::get('/info/:id', 'sys/auth.Menu/info');
    // 添加菜单
    Route::post('/save', 'sys/auth.Menu/save');
    // 修改菜单
    Route::post('/update', 'sys/auth.Menu/update');
    // 删除菜单
    Route::post('/delete/:id', 'sys/auth.Menu/delete');
});

Route::group('sys/role',function(){
    // 获取角色列表
    Route::get('/list', 'sys/auth.Role/list');
    // 获取角色列表, 根据当前用户
    Route::get('/select', 'sys/auth.Role/select');
    // 获取角色信息
    Route::get('/info/:id', 'sys/auth.Role/info');
    // 添加角色
    Route::post('/save', 'sys/auth.Role/save');
    // 修改角色
    Route::post('/update', 'sys/auth.Role/update');
    // 删除角色
    Route::post('/delete', 'sys/auth.Role/delete');
});


