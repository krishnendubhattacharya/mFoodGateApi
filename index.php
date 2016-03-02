<?php

require 'Slim/Slim.php';
require 'config.php';
require 'class/class.phpmailer.php';
require 'sendmail.php';
require 'service/crud.php';
require 'service/service.php';
require 'service/user.php';
require 'service/coupon.php';
require 'service/category.php';
require 'service/voucher.php';
require 'service/points.php';

$app = new Slim();
$app->config('debug', true);

$app->get('/wines', 'getWines');
$app->get('/wines/:id',	'getWine');
$app->get('/wines/search/:query', 'findByName');
$app->post('/wines', 'addWine');
$app->put('/wines/:id', 'updateWine');
$app->delete('/wines/:id','deleteWine');

$app->post('/users', 'addUser');
$app->post('/users/login', 'getlogin');
$app->post('/users/forgotPass', 'getforgetPass');
$app->post('/users/changePassword', 'changePassword');
$app->put('/users/:id', 'updateUser');
$app->put('/users/activeProfile/:id', 'activeProfile');
$app->get('/users', 'getAllUsers');
$app->get('/user/:id',	'getUser');
$app->post('/users/logout', 'getlogout');
$app->delete('/users/:id','deleteUser');
$app->post('/fbloginuser', 'fbLoginUser');
$app->post('/profileimageupload', 'profileImageUpload');
$app->get('/testmail', 'testMail');

$app->post('/coupons', 'addCoupon');
$app->get('/coupons', 'getAllCoupons');
$app->put('/coupons/:id', 'updateCoupon');
$app->delete('/coupons/:id','deleteCoupon');

$app->post('/categories',  'addCat');
$app->get('/categories',  'getAllCats');
$app->get('/category/:id', 'getCat');
$app->put('/categories/:id', 'updateCat');
$app->delete('/categories/:id', 'deleteCat');

$app->get('/allvoucher/:user_id',  'getAllActiveVoucher');
$app->get('/expiresoonvoucher/:user_id', 'getExpireSoonVoucher');
$app->get('/vourcherdetail/:id', 'getVoucherUserMerchentDetail');
$app->post('/resale', 'addResale');
$app->get('/ownresellList/:id','getResellListPostOwn');
$app->get('/othersresellList/:id','getResellListPostOthers');
$app->get('/bidders/:id/:userid','getBidderList');
$app->post('/bids', 'addBid');
$app->get('/ownbid/:userid', 'getResellListBidOwn');

$app->get('/mypoints/:user_id',  'getMyPoints');

$app->response()->header("Content-Type", "application/json");
$app->response()->header("Access-Control-Allow-Origin : * ");
$app->response()->header("Access-Control-Allow-Methods : POST, GET, OPTIONS, DELETE, PUT ");


$app->run();

?>
