<?php

require 'Slim/Slim.php';
require 'config.php';
require 'sendmail.php';
require 'service/crud.php';
require 'service/service.php';
require 'service/user.php';
require 'service/coupon.php';
require 'service/category.php';

$app = new Slim();

$app->get('/wines', 'getWines');
$app->get('/wines/:id',	'getWine');
$app->get('/wines/search/:query', 'findByName');
$app->post('/wines', 'addWine');
$app->put('/wines/:id', 'updateWine');
$app->delete('/wines/:id','deleteWine');

$app->post('/users', 'addUser');
$app->post('/users/login', 'getlogin');
$app->post('/users/forgotPass', 'getforgetPass');
$app->put('/users/:id', 'updateUser');
$app->put('/users/activeProfile/:id', 'activeProfile');
$app->get('/users', 'getAllUsers');
$app->get('/user/:id',	'getUser');
$app->post('/users/logout', 'getlogout');
$app->delete('/users/:id','deleteUser');
$app->post('/fbloginuser', 'fbLoginUser');
$app->post('/profileimageupload', 'profileImageUpload');

$app->post('/coupons', 'addCoupon');
$app->get('/coupons', 'getAllCoupons');
$app->put('/coupons/:id', 'updateCoupon');
$app->delete('/coupons/:id','deleteCoupon');

$app->post('/categories',  'addCat');
$app->get('/categories',  'getAllCats');
$app->get('/category/:id', 'getCat');
$app->put('/categories/:id', 'updateCat');
$app->delete('/categories/:id', 'deleteCat');


$app->response()->header("Content-Type", "application/json");
$app->run();

?>
