<?php
require 'Slim/Slim.php';
require 'config.php';
require 'class/class.phpmailer.php';
require_once 'dompdf/vendor/autoload.php';
require_once 'qrcode/qrlib.php';
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
require 'service/email_templates.php';
require 'service/sms.php';
require 'service/bank_details.php';
require 'service/banner.php';
require 'service/advertisement.php';
require 'service/point_master.php';
require 'service/blogs.php';
require 'service/cart.php'; 
require 'service/qrcode.php';
require_once 'service/voucher_pdf.php';
require_once 'service/merchantrestaurants.php';
require_once 'service/merchantoutlet.php';

 
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
$app->get('/getUserSettings/:id','getUserSettings');
$app->put('/updateUserSettings/:id','updateUserSettings');
$app->get('/getActiveMerchants','getActiveMerchants');
$app->get('/getUserByEmail/:email','getUserByEmail');
$app->post('/addSubUser','addSubUser');
$app->get('/getAllSubUserByUser/:id','getAllSubUserByUser');
$app->post('/updateSubUser','updateSubUser');

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
$app->get('/getAllFeaturedPromoAds',  'getAllFeaturedPromoAds');
$app->get('/getLaunchTodayPromo',  'getLaunchTodayPromo');
$app->get('/getLastdayPromo',  'getLastdayPromo');
$app->get('/getHotSellingPromo',  'getHotSellingPromo');
$app->get('/getSpecialPromo',  'getSpecialPromo');
$app->get('/registerMemberApi/:email',  'registerMemberApi');

/************* Vouchers ****************/
$app->get('/getResturantLaunchTodayPromo/:id',  'getResturantLaunchTodayPromo');
$app->get('/getResturantHotSellingPromo/:id',  'getResturantHotSellingPromo');
$app->get('/getResturantLastDayPromo/:id',  'getResturantLastDayPromo');
$app->get('/getResturantSpecialPromo/:id',  'getResturantSpecialPromo');

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
$app->post('/resaleCancel', 'resaleCancel');
$app->get('/getDetailsVoucherBid/:id', 'getDetailsVoucherBid');
$app->post('/updateVoucherBid', 'updateVoucherBid');
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
$app->get('/getPromoDetailsAdmin/:id','getPromoDetailsAdmin');
$app->get('/getRelatedPromo/:id','getRelatedPromo');
$app->get('/getMyMembership/:userid','getMyMembership');
$app->get('/getMyExpiredMembership/:userid','getMyExpiredMembership');
$app->get('/getMyMembershipExpireSoon/:userid','getMyMembershipExpireSoon');
$app->post('/saveOffer', 'addOffer');
$app->get('/getAllActiveMembership/:userid','getAllActiveMembership');
$app->post('/merchantSaveMember','merchantSaveMember');
$app->get('/getLocations',  'getAllActiveLocation'); 
$app->post('/giftVoucher', 'giftVoucher');
$app->get('/getGiftedToMe/:userid', 'getGiftedToMe');
$app->get('/getGiftedByMe/:userid', 'getGiftedByMe');
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
$app->get('/getActiveRestaurants', 'getActiveRestaurants');
$app->get('/getResturantByMerchant/:uid', 'getResturantByMerchant');

/**************** Outlets *************/
$app->get('/getOutletsByRestaurant/:id', 'getOutletsByRestaurant');
$app->post('/getOutletsBySelectedRestaurant', 'getOutletsBySelectedRestaurant');
$app->post('/addOutlet', 'addOutlet');
$app->post('/updateOutlet', 'updateOutlet');
$app->delete('/deleteOutlet/:id', 'deleteOutlet');
$app->get('/getOutletDetails/:id', 'getOutletDetails');
$app->get('/getOutletsByUser/:user_id','getOutletsByUser');
$app->post('/outletFileUpload', 'outletFileUpload');
$app->post('/addOutletMerchant', 'addOutletMerchant');
$app->post('/updateOutletMerchant', 'updateOutletMerchant');

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
$app->post('/addMerchantNews',  'addMerchantNews');
$app->post('/newsFileUpload',  'newsFileUpload');
$app->get('/getNewsByMerchant/:id', 'getNewsByMerchant');
$app->post('/updateMerchantNews',  'updateMerchantNews');

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
$app->get('/mySwapList/:id', 'mySwapList');
$app->get('/otherSwapList/:id', 'otherSwapList');
$app->get('/myBidSwapList/:id', 'myBidSwapList');
$app->get('/interestedSwapList/:id', 'interestedSwapList');
$app->post('/swapCancel', 'swapCancel');
$app->post('/updateSwapBid', 'updateSwapBid');
$app->get('/getDetailsSwapBid/:id', 'getDetailsSwapBid');
$app->get('/swaplist', 'swaplist');

$app->get('/interestedSwapList/:id', 'interestedSwapList');
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
$app->get('/getAllOffers',  'getAllOffers');
$app->post('/checkExpiredOffers',  'checkExpiredOffers');
$app->get('/getOfferImages/:id',  'getOfferImages');
$app->post('/addNewOfferImage',  'addNewOfferImage');

/***************** Point Master ************/
$app->get('/getAllPointMaster',  'getAllPointMaster');
$app->post('/addNewPointMaster',  'addNewPointMaster');
$app->post('/updatePointMaster',  'updatePointMaster');
$app->delete('/deletePointMaster/:id',  'deletePointMaster');
$app->get('/getActivePointMasterByMerchant/:id',  'getActivePointMasterByMerchant');

/**************** Blogs *******************/
$app->get('/getAllBlogs',  'getAllBlogs');
$app->post('/addNewBlog',  'addNewBlog');

/***************** Membership *************/
$app->post('/addNewMembership',  'addNewMembership'); 
$app->get('/deleteMembership/:id',  'deleteMembership'); 
$app->get('/getMembershipByMerchant/:id',  'getMembershipByMerchant'); 


/****************** Offer Types ***********/
$app->get('/getAllActiveOfferType',  'getAllActiveOfferType');

/************************ Email Templates ********************/
$app->post('/addEmailTemplate',  'addEmailTemplate'); 
$app->get('/getAllEmailTemplates',  'getAllEmailTemplates');
$app->post('/updateEmailTemplate',  'updateEmailTemplate');
$app->delete('/deleteEmailTemplate/:id',  'deleteEmailTemplate');

/*********************** SMS *********************************/
$app->post('/addSms',  'addSms'); 
$app->get('/getAllSms',  'getAllSms');
$app->post('/updateSms',  'updateSms');
$app->delete('/deleteSms/:id',  'deleteSms');

/***************** Bank Details ************************/
$app->post('/addBankDetails',  'addBankDetails');
$app->get('/getBankDetailsByUser/:id',  'getBankDetailsByUser');
$app->post('/updateBankDetails',  'updateBankDetails');

/********************Banner***********************/
$app->post('/addBanner', 'addBanner');
$app->get('/getAllBanner', 'getAllBanner');
$app->get('/getBannerDetails/:id', 'getBannerDetails');
$app->put('/updateBanner/:id', 'updateBanner');
$app->delete('/deleteBanner/:id', 'deleteBanner');
$app->get('/getActiveBanner', 'getActiveBanner');
$app->get('/getActiveBannerOther/:id', 'getActiveBannerOther');
$app->get('/getUpdateBannerOther/:id', 'getUpdateBannerOther');
$app->get('/getHotBanner', 'getHotBanner');
$app->get('/getBannersClicked/:banid/(:userid)', 'getBannersClicked');

/********************Advertisement***********************/
$app->post('/addAds', 'addAds');
$app->get('/getAllAds', 'getAllAds');
$app->get('/getAdsDetails/:id', 'getAdsDetails');
$app->put('/updateAds/:id', 'updateAds');
$app->delete('/deleteAds/:id', 'deleteAds');
$app->get('/getAllAdsLocation', 'getAllAdsLocation');
$app->get('/getAdsClicked/:adid/(:userid)', 'getAdsClicked'); 
$app->get('/getActiveAdsByLocation/:id', 'getActiveAdsByLocation');
$app->get('/getHotAds', 'getHotAds');
$app->get('/getUpdateAdvOther/:id', 'getUpdateAdvOther');
$app->get('/getActiveAdvOther/:id', 'getActiveAdvOther');

/****************** Cart ****************/
$app->post('/addToCart', 'addToCart');
$app->post('/deleteFromCart', 'deleteFromCart');
$app->delete('/deleteCartByUser/:user_id', 'deleteCartByUser');
$app->get('/getCartByUser/:user_id', 'getCartByUser');
$app->post('/updateCartQuantity', 'updateCartQuantity');

/******************* Voucher Pdf *****************/
$app->get('/downloadVoucherPdf/:vid', 'downloadVoucherPdf');

/******************* Merchant Restaurant ***************/
$app->get('/getMerchantsRestaurants/:merchant_id', 'getMerchantsRestaurants');
$app->post('/MerchantRestaurantLogoUpload', 'MerchantRestaurantLogoUpload'); 
$app->post('/addMerchantsResturant','addMerchantsResturant');
$app->post('/updateMerchantsResturant','updateMerchantsResturant');
$app->delete('/deleteMerchantRestaurant/:id','deleteMerchantRestaurant'); 
$app->get('/getActiveMerchantRestaurant/:merchant_id', 'getActiveMerchantRestaurant');

/******************* Merchant Outlet *******************/
$app->post('/addMerchantOutlet','addMerchantOutlet');
$app->get('/getMerchantsOutlet/:merchant_id', 'getMerchantsOutlet');
$app->post('/MerchantOutletFileUpload','MerchantOutletFileUpload');
$app->post('/updateMerchantOutlet','updateMerchantOutlet');
$app->delete('/deleteMerchantOutlet/:merchant_id', 'deleteMerchantOutlet');

/******************* Merchant News ***************/

/******************** QR Code ********************/
$app->get('/genVoucherQrCode', 'genVoucherQrCode');

/*********************** Admin ************************/
/******************** Users *************************/
$app->post('/users/adminlogin', 'adminlogin');
/*********************** Admin ************************/
$app->response()->header("Content-Type", "application/json");
//$app->response()->header("Access-Control-Allow-Origin : * ");
//$app->response()->header("Access-Control-Allow-Methods : POST, GET, OPTIONS, DELETE, PUT ");
$app->run();
?>
