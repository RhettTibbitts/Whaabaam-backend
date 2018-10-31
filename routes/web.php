<?php

//API ROUTES
Route::post('api/login', 'Api\AuthController@login');
Route::post('api/signup', 'Api\AuthController@signup');
Route::post('api/forgot-password', 'Api\AuthController@forgot_password');
Route::post('api/forgot-password/verify', 'Api\AuthController@forgot_password_verify');
Route::post('api/forgot-password/set-password', 'Api\AuthController@forgot_password_set_password');

Route::post('api/get-cities', 'Api\ProfileController@get_cities');

Route::group(['prefix'=>'api/', 'middleware'=>'verifyApiToken'], function(){
	
	Route::post('logout', 'Api\AuthController@logout');
	Route::post('change-password', 'Api\AuthController@change_password');

	Route::post('profile', 'Api\ProfileController@view_my_profile');
	Route::post('profile/edit', 'Api\ProfileController@edit');

	//notification preferences
	Route::post('capture-options', 'Api\ProfileController@show_capture_options');
	Route::post('capture-options/edit', 'Api\ProfileController@edit_capture_options');

	//save location regular call service in app
	Route::post('location/add', 'Api\CaptureController@save_location');

	//profile image
	Route::post('profile/pic/upload', 'Api\ProfilePictureController@upload_profile_pic');
	Route::post('profile/pic/delete', 'Api\ProfilePictureController@delete_profile_pic');
	Route::post('profile/main-pic/delete', 'Api\ProfilePictureController@delete_profile_main_pic');
	Route::post('profile/main-pic/set', 'Api\ProfilePictureController@set_profile_main_pic');
	Route::post('profile/resume/delete', 'Api\ProfilePictureController@delete_resume');

	Route::post('captured-users', 'Api\CaptureController@index');
	Route::post('search', 'Api\SearchController@index');
	
	Route::post('profile/view', 'Api\ProfileController@view_another_profile');

	Route::post('profile/notes', 'Api\UserNoteController@index');
	Route::post('profile/note/add', 'Api\UserNoteController@add');
	Route::post('profile/note/edit', 'Api\UserNoteController@edit');
	Route::post('profile/note/delete', 'Api\UserNoteController@delete');

	//friend requests
	Route::post('friend-req/send', 'Api\FriendRequestController@send_request');
	Route::post('friend-req/response', 'Api\FriendRequestController@response_request');
	Route::post('friend-req/cancel', 'Api\FriendRequestController@cancel_request'); //self cancel request
	Route::post('friends', 'Api\FriendRequestController@friends');
	Route::post('friend/unfriend', 'Api\FriendRequestController@unfriend');
	Route::post('friendship/check', 'Api\FriendRequestController@check_is_friend');

	//family
	Route::post('family/show-relations', 'Api\FamilyMemberController@show_relations');
	Route::post('family/add-friend-list', 'Api\FamilyMemberController@show_friends_to_add_in_family');
	Route::post('family-member/add', 'Api\FamilyMemberController@add');
	Route::post('family-member/delete', 'Api\FamilyMemberController@delete');
	Route::post('family-members', 'Api\FamilyMemberController@my_family_members');

	//mutual friends
	Route::post('mutual-friends', 'Api\FriendRequestController@mutual_friends');

	Route::post('notifications', 'Api\NotificationController@index');

	Route::post('report', 'Api\ReportController@add');

	Route::post('report-err', 'Api\ProfileController@send_error_email');
	
});

//cron job
Route::match(['get','post'],'api/send_push','Api\NotificationController@send_push');
Route::get('api/cron/capture-users','CronController@capture_users');

// Route::get('test', 'FrontEndController@test'); //for testing purpose only

// --------------------------FRONTEND ROUTES ------------------------//

Route::post('/contact-us', 'FrontEndController@contact_us');
Route::post('/contact-us', 'FrontEndController@contact_us');
Route::get('terms' , 'FrontEndController@terms_conditions');

Route::get('/', function(){
	return redirect('/admin');
});

// --------------------------BACKEND ROUTES ------------------------//
Route::match(['get','post'],'admin/login', 'backEnd\AuthController@login');
Route::get('admin/logout', 'backEnd\AuthController@logout');
Route::get('admin/', 'backEnd\DashboardController@index');
Route::match(['get','post'],'admin/forgot-password', 'backEnd\AuthController@forgot_password');
Route::match(['get','post'],'admin/reset-password', 'backEnd\AuthController@reset_password');

Route::group(['prefix'=>'admin', 'middleware'=>'verifyAdminAuth'], function(){

	//admin profile
	Route::get('/profile', 'backEnd\AdminController@index');
	Route::match(['get','post'],'/profile/edit', 'backEnd\AdminController@edit_profile');
	Route::match(['get','post'],'/change-password', 'backEnd\AdminController@change_password');

	Route::get('/dashboard', 'backEnd\DashboardController@index');

	//User Management
	Route::match(['get','post'],'/users', 'backEnd\UserManagement@index');
	Route::match(['get','post'],'/user/add', 'backEnd\UserManagement@add');
	Route::match(['get','post'],'/user/edit/{user_id}', 'backEnd\UserManagement@edit');
	Route::post('/user/delete/{user_id}', 'backEnd\UserManagement@delete');
	Route::get('/user/image/delete/{usr_img_id}', 'backEnd\UserManagement@delete_image');
	Route::get('/user/resume/delete/{user_id}', 'backEnd\UserManagement@delete_resume');
	Route::get('/user/main-image/delete/{user_id}', 'backEnd\UserManagement@delete_main_image');


	//State Management
	Route::match(['get','post'],'/states', 'backEnd\system\StateController@index');
	Route::match(['get','post'],'/state/add', 'backEnd\system\StateController@add');
	Route::match(['get','post'],'/state/edit/{state_id}', 'backEnd\system\StateController@edit');
	Route::get('/state/delete/{state_id}', 'backEnd\system\StateController@delete');

	//City Management
	Route::match(['get','post'],'/cities/{state_id}', 'backEnd\system\CityController@index');
	Route::match(['get','post'],'/city/add/{state_id}', 'backEnd\system\CityController@add');
	Route::match(['get','post'],'/city/edit/{city_id}', 'backEnd\system\CityController@edit');
	Route::get('/city/delete/{city_id}', 'backEnd\system\CityController@delete');

	Route::match(['get','post'],'/militaries', 'backEnd\system\MilitaryController@index');
	Route::match(['get','post'],'/military/add', 'backEnd\system\MilitaryController@add');
	Route::match(['get','post'],'/military/edit/{military_id}', 'backEnd\system\MilitaryController@edit');
	Route::get('/military/delete/{military_id}', 'backEnd\system\MilitaryController@delete');

	Route::match(['get','post'],'/politicals', 'backEnd\system\PoliticalController@index');
	Route::match(['get','post'],'/political/add', 'backEnd\system\PoliticalController@add');
	Route::match(['get','post'],'/political/edit/{political_id}', 'backEnd\system\PoliticalController@edit');
	Route::get('/political/delete/{political_id}', 'backEnd\system\PoliticalController@delete');

	Route::match(['get','post'],'/relationships', 'backEnd\system\RelationshipController@index');
	Route::match(['get','post'],'/relationship/add', 'backEnd\system\RelationshipController@add');
	Route::match(['get','post'],'/relationship/edit/{relationship_id}', 'backEnd\system\RelationshipController@edit');
	Route::get('/relationship/delete/{relationship_id}', 'backEnd\system\RelationshipController@delete');

	Route::match(['get','post'],'/religions', 'backEnd\system\ReligionController@index');
	Route::match(['get','post'],'/religion/add', 'backEnd\system\ReligionController@add');
	Route::match(['get','post'],'/religion/edit/{religion_id}', 'backEnd\system\ReligionController@edit');
	Route::get('/religion/delete/{religion_id}', 'backEnd\system\ReligionController@delete');

	Route::match(['get','post'],'/emails', 'backEnd\system\EmailController@index');
	Route::match(['get','post'],'/email/add', 'backEnd\system\EmailController@add');
	Route::match(['get','post'],'/email/edit/{email_id}', 'backEnd\system\EmailController@edit');
	Route::get('/email/delete/{email_id}', 'backEnd\system\EmailController@delete');

	Route::get('/pages', 'backEnd\system\CmsPageController@index');
	Route::match(['get','post'],'/page/{page_id}', 'backEnd\system\CmsPageController@edit');


});

//COMMON ROUTES
//location
Route::get('/location/get-states/{country_id}', 'LocationController@get_states');
Route::get('/location/get-cities/{state_id}', 'LocationController@get_cities');

//CONSTANTS
define('COMMON_ERR','Some error occured, please try again after sometime');
define('AUTH_ERR','Please login to access this page');
define('API_AUTH_ERR','You are not authorized');

//image paths
//USER_PROFILE_BASE_PATH
define('USER_PROFILE_IMG_PATH','public/images/user_profile/');
define('USER_PROFILE_MAIN_IMG_PATH','public/images/user_profile/main/');
define('USER_RESUME_PATH','public/images/user_profile/resume/');
define('ADMIN_IMG_PATH','public/images/admin/');
define('SYS_IMG_PATH','public/images/system/');


define('IOS_APNS_SERVER','ssl://gateway.push.apple.com:2195'); //producton
// define('IOS_APNS_SERVER','ssl://gateway.sandbox.push.apple.com:2195'); //development

define('MAX_USER_IMG_COUNT',3);
define('PROJECT_NAME','WhaaBaam');

define('LOCATION_UPDATE_DEFAULT_INTERVAL',5); //in mint 
define('ALIVE_CHECK_TIME',7);
define('FCM_API_KEY','AIzaSyD23rAhqu5P-OmSmbdbZOX9yUnhvzioFlo');
define('PEM_FILE_PATH',base_path('public/whaabaam.pem'));

define('MAX_USER_PICS',3); //maximum no. of images that a user can upload
define('PAGINATE_LIMIT',10); //maximum no. of images that a user can upload

define('QUICKBLOX_APP_ID',env('QUICKBLOX_APP_ID',''));
define('QUICKBLOX_AUTH_KEY',env('QUICKBLOX_AUTH_KEY',''));
define('QUICKBLOX_AUTH_SECRET',env('QUICKBLOX_AUTH_SECRET',''));

