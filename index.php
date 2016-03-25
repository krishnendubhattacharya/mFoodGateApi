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
require 'service/location.php';
require 'service/resturant.php';
require 'service/news.php';
require 'service/contents.php';
require 'service/sitesettings.php';
require 'service/paypal.php';
require 'service/swap.php';

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
$app->get('/clientlist/:id', 'getAllClientUser');

$app->post('/coupons', 'addCoupon');
$app->get('/coupons', 'getAllCoupons');
$app->put('/coupons/:id', 'updateCoupon');
$app->delete('/coupons/:id','deleteCoupon');

$app->post('/categories',  'addCat');
$app->get('/categories',  'getAllCats');
$app->get('/getCategories',  'getAllActiveCats');
$app->get('/getFeaturedCategories',  'getAllFeaturedCats');
$app->get('/getLaunchTodayPromo',  'getLaunchTodayPromo');
$app->get('/getLastdayPromo',  'getLastdayPromo');
$app->get('/getHotSellingPromo',  'getHotSellingPromo');
$app->get('/getSpecialPromo',  'getSpecialPromo');
$app->get('/getMenuPromo',  'getMenuPromo');
$app->get('/getPaymentPromo',  'getPaymentPromo');
$app->get('/getMerchantPromo',  'getMerchantPromo');
$app->get('/getResturantPromo/:rid',  'getResturantPromo');
$app->get('/category/:id', 'getCat');
$app->put('/categories/:id', 'updateCat');
$app->delete('/categories/:id', 'deleteCat');

$app->get('/allvoucher/:user_id',  'getAllActiveVoucher');
$app->get('/ownoffer/:user_id',  'getOwnOffer');
$app->get('/expiresoonvoucher/:user_id', 'getExpireSoonVoucher');
$app->get('/vourcherdetail/:id', 'getVoucherUserMerchentDetail');
$app->get('/resellVoucherDetail/:vid/:resellid', 'resellVoucherDetail');
$app->post('/resale', 'addResale');
$app->get('/ownresellList/:id','getResellListPostOwn');
$app->get('/othersresellList/:id','getResellListPostOthers');
$app->get('/bidders/:id/:userid','getBidderList');
$app->post('/bids', 'addBid');
$app->get('/ownbid/:userid', 'getResellListBidOwn');
$app->post('/saveVoucherResale', 'saveVoucherResale');
$app->get('/offerdetail/:id', 'getOfferDetail');
$app->post('/offerImageUpload', 'offerImageUpload');
$app->get('/getAllResellList', 'getAllResellList');
$app->get('/getPromoDetails/:id','getPromoDetails');
$app->get('/getRelatedPromo','getRelatedPromo');
$app->get('/getMyMembership/:userid','getMyMembership');
$app->get('/getMyMembershipExpireSoon/:userid','getMyMembershipExpireSoon');
$app->post('/saveOffer', 'addOffer');


$app->get('/getLocations',  'getAllActiveLocation');
$app->post('/giftVoucher', 'giftVoucher');
$app->get('/getPromoList',  'getPromoList');
$app->get('/getExpireSoonPromoList',  'getExpireSoonPromoList');

/***********Restuarant Call***********/
$app->get('/getFeaturedResturantHome', 'getFeaturedResturantHome');
$app->get('/getResturantByCategory/:cid', 'getResturantByCategory');

/***********News Call***********/
$app->post('/addNews', 'addNews');
$app->get('/getNewsListAll', 'getNewsListAll');
$app->get('/getNewsMostViews', 'getNewsMostViews');
$app->get('/getNewsLatest', 'getNewsLatest');
$app->get('/getNewsBanner', 'getNewsBanner');
$app->get('/getNewsDetail/:id', 'getNewsDetail');
$app->get('/getNewsList', 'getNewsList');
$app->post('/updateNews', 'updateNews');
$app->delete('/deleteNews/:news_id', 'deleteNews');


/***********Contents Call***********/
$app->get('/getContent/:page_header', 'getContent');

/***********SiteSettings Call***********/
$app->get('/getSiteSetting', 'getSiteSetting');

/***********Paypal Call***********/
$app->post('/cart_checkout', 'cart_checkout');
$app->post('/success_payment', 'success_payment');

/*************Swap Call***********/
$app->post('/swap', 'addSwap');
$app->get('/swaplist', 'swaplist');
$app->get('/swapdetails/:sid', 'swapdetails'); 
$app->post('/swapinterest', 'swapinterest');  

/**********points Call*********/
$app->get('/mypoints/:user_id',  'getMyPoints');
$app->get('/expiresoonpoints/:user_id',  'getExpireSoonPoints');
$app->get('/getUsersPoints/:user_id',  'getUsersPoints');
$app->post('/redeemUserPoints',  'redeemUserPoints');
$app->get('/swapInterestAccept/:siid',  'swapInterestAccept');


$app->response()->header("Content-Type", "application/json");
//$app->response()->header("Access-Control-Allow-Origin : * ");
//$app->response()->header("Access-Control-Allow-Methods : POST, GET, OPTIONS, DELETE, PUT ");


$app->run();

?>

