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
require 'service/menus.php';
require 'service/events.php';
require 'service/event_bids.php';
require 'service/outlets.php';
require 'service/search.php';
require 'service/offer_types.php';
require 'service/offers.php';
require 'service/membership.php';
 
$app = new Slim();
$app->config('debug', true);
$app->get('/wines', 'getWines');
$app->get('/wines/:id',	'getWine');
$app->get('/wines/search/:query', 'findByName');
$app->post('/wines', 'addWine');
$app->put('/wines/:id', 'updateWine');
$app->delete('/wines/:id','deleteWine');
/***************** Users ************/
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
$app->get('/getAllMerchants','getAllMerchants');
$app->get('/getCustomer/:id','getCustomer');
$app->get('/getAllCustomers','getAllCustomers');

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

$app->get('/getActiveMembershipPromo',  'getActiveMembershipPromo');
$app->get('/getLaunchTodayMembership',  'getLaunchTodayMembership');
$app->get('/getLastdayMembership',  'getLastdayMembership');
$app->get('/getHotSellingMembership',  'getHotSellingMembership');
$app->get('/getSpecialMembership',  'getSpecialMembership');

$app->get('/getActiveMenuPromo',  'getActiveMenuPromo');
$app->get('/getLaunchTodayMenuPromo',  'getLaunchTodayMenuPromo');
$app->get('/getLastdayMenuPromo',  'getLastdayMenuPromo');
$app->get('/getHotSellingMenuPromo',  'getHotSellingMenuPromo');
$app->get('/getSpecialMenuPromo',  'getSpecialMenuPromo');
$app->get('/getLaunchTodayPaymentPromo',  'getLaunchTodayPaymentPromo');

$app->get('/getActivePaymentPromo',  'getActivePaymentPromo');
$app->get('/getLastdayPaymentPromo',  'getLastdayPaymentPromo');
$app->get('/getHotSellingPaymentPromo',  'getHotSellingPaymentPromo');
$app->get('/getSpecialPaymentPromo',  'getSpecialPaymentPromo');
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
$app->get('/getMyExpiredMembership/:userid','getMyExpiredMembership');
$app->get('/getMyMembershipExpireSoon/:userid','getMyMembershipExpireSoon');
$app->post('/saveOffer', 'addOffer');
$app->get('/getAllActiveMembership/:userid','getAllActiveMembership');
$app->post('/merchantSaveMember','merchantSaveMember');
$app->get('/getLocations',  'getAllActiveLocation'); 
$app->post('/giftVoucher', 'giftVoucher');
$app->get('/getPromoList',  'getPromoList');
$app->get('/getExpireSoonPromoList',  'getExpireSoonPromoList');
$app->post('/addMerchant',  'addMerchant');
/***********Restuarant Call***********/
$app->get('/getFeaturedResturantHome', 'getFeaturedResturantHome');
$app->get('/getResturantByCategory/:cid', 'getResturantByCategory');
$app->get('/getAllRestaurantWithUser', 'getAllRestaurantWithUser');
$app->post('/addResturant', 'addResturant');
$app->post('/restaurantLogoUpload', 'restaurantLogoUpload');
$app->get('/getRestaurantDetails/:id', 'getRestaurantDetails');
$app->put('/updateResturant/:id', 'updateResturant');
$app->delete('/deleteResturant/:id', 'deleteResturant');
$app->get('/getRestaurantByMerchant/:id', 'getRestaurantByMerchant');

/**************** Outlets *************/
$app->get('/getOutletsByRestaurant/:id', 'getOutletsByRestaurant');
$app->post('/addOutlet', 'addOutlet');
$app->post('/updateOutlet', 'updateOutlet');
$app->delete('/deleteOutlet/:id', 'deleteOutlet');
$app->get('/getOutletDetails/:id', 'getOutletDetails');

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
$app->post('/updateSiteSettings', 'updateSiteSettings');
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

/*************** Menus ***************/
$app->post('/addMenu',  'addMenu');
$app->get('/getMenuByUser/:user_id',  'getMenuByUser');
$app->post('/menuFileUpload',  'menuFileUpload');
$app->post('/updateMenu',  'updateMenu'); 
$app->delete('/deleteMenu/:menu_id', 'deleteMenu'); 

/*************** Events  *************/
$app->post('/eventFilesUpload',  'eventFilesUpload');
$app->get('/getEventsByUser/:user_id',  'getEventsByUser');
$app->post('/addEvent',  'addEvent');
$app->put('/updateEvent/:id', 'updateEvent');
$app->get('/getEvenDetails/:id', 'getEvenDetails');
$app->get('/getImagesByEvent/:id', 'getImagesByEvent');
$app->post('/addEventImage',  'addEventImage');
$app->get('/getActiveEvents',  'getActiveEvents');
$app->get('/getMerchantsRelatedEvents/:id',  'getMerchantsRelatedEvents'); 

/*************** Event Bids ****************/
$app->get('/getEventBidsWithUser/:event_id', 'getEventBidsWithUser');
$app->get('/acceptEventBid/:event_id/:bid_id', 'acceptEventBid');
$app->post('/addEventBid', 'addEventBid');

/**************** Locations **************/
$app->get('/getAllLocations',  'getAllLocations');
$app->post('/addLocation',  'addLocation');
$app->put('/updateLocation/:id',  'updateLocation');
$app->delete('/deleteLocation/:id',  'deleteLocation');
$app->get('/getCountries',  'getCountries');
$app->get('/getLocationsWithCountry',  'getLocationsWithCountry'); 

/****************** Search **************/
$app->get('/getSiteSearch',  'getSiteSearch');

/****************** Offers ***************/
$app->post('/addNewOffer',  'addNewOffer');
$app->post('/updateOffer',  'updateOffer');
$app->delete('/deleteOffer',  'deleteOffer');
$app->get('/getOffersByRestaurant/:id',  'getOffersByRestaurant');

/***************** Membership *************/
$app->post('/addNewMembership',  'addNewMembership'); 


/****************** Offer Types ***********/
$app->get('/getAllActiveOfferType',  'getAllActiveOfferType');

/*********************** Admin ************************/
/******************** Users *************************/
$app->post('/users/adminlogin', 'adminlogin');
/*********************** Admin ************************/
$app->response()->header("Content-Type", "application/json");
//$app->response()->header("Access-Control-Allow-Origin : * ");
//$app->response()->header("Access-Control-Allow-Methods : POST, GET, OPTIONS, DELETE, PUT ");
$app->run();
?>
