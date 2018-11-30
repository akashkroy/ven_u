<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/* Root */

Route::get('/', 'HomeController@getIndex');

/* Legal */

Route::get('/legal/privacy-policy', 'HomeController@getPrivacyPolicy');
Route::get('/legal/copyright-policy', 'HomeController@getCopyrightPolicy');
Route::get('/legal/terms-of-service', 'HomeController@getTermsOfService');
Route::get('/about-us', 'HomeController@getAboutUs');
Route::get('/faq', 'HomeController@getFAQ');

/* Registration */

Route::get('/register', 'UserController@getRegister');
Route::get('/artist-register', 'UserController@getRegisterAsArtist');
Route::post('/artist-register', 'UserController@postRegisterAsArtist');
Route::post('/artist-register-materials', 'UserController@postRegisterMaterials');
Route::get('/artist-register-materials','UserController@getRegisterMaterials');
Route::get('/venue-register', 'UserController@getRegisterAsVenue');
Route::post('/venue-register', 'UserController@postRegisterAsVenue');
Route::get('/validate-email', 'UserController@getValidateEmail');

/* Login and Logout */

Route::get('/login', 'UserController@getLogin');
Route::post('/login/artist', 'UserController@postLoginArtist');
Route::post('/login/venue', 'UserController@postLoginVenue');
Route::get('/logout', 'UserController@getLogout');

Route::post('/reset-password', 'UserController@postForgotPassword');

/* Users */

// View public profile
Route::get('/profile', 'UserController@getUserProfile');
Route::get('/profile/{id}', 'UserController@getPublicProfile');

// Autocomplete
Route::get('/autocomplete-artists', 'UserController@getAutocompleteArtists');
Route::get('/autocomplete-venues', 'UserController@getAutocompleteVenues');

// Google Calendar
Route::get('/authenticate-gc/', 'EventController@authenticateGoogleCalendar');
Route::get('/update-google-cal/', 'EventController@insertEventGoogleCalendar');
Route::get('/get-email/', 'EventController@getAuthenticatedUserEmail');
Route::get('/get-event/', 'EventController@getGoogleEvent');
Route::get('/gc/integrate/disable', 'EventController@getDisableIntegrateGoogleCalendar');

/* Events */

// Create event
Route::get('/event/create', 'EventController@getCreateEvent');
Route::post('/event/create', 'EventController@postCreateEvent');
Route::post('/event/create-from-drag-and-drop', 'EventController@postCreateEventFromDragAndDrop');

// Get event
Route::get('/event/{id}', 'EventController@getEvent');

// Edit event
Route::get('/event/edit/{id}', 'EventController@getEditEvent');
Route::post('/event/edit/{id}', 'EventController@postEditEvent');

// Cancel event
Route::get('/event/cancel/{id}', 'EventController@getCancelEvent');

// Delete event (enable if needed)
//Route::get('/event/delete/{id}', 'EventController@getDeleteEvent');

// Events feed
Route::get('/events-feed/calendar', 'EventController@getEventsFeedCalendar');

// My events
Route::get('/my-events/events', 'EventController@getMyEventsEvents');
Route::get('/my-events/calendar', 'EventController@getMyEventsCalendar');

// Fetch events
Route::post('/event/ajax-fetch-private-events-by-user-id', 'EventController@postAjaxFetchPrivateEventsByUserId');
Route::post('/event/ajax-fetch-public-events-by-user-id', 'EventController@postAjaxFetchPublicEventsByUserId');

// Edit artist lineup equipment

Route::post('/event/edit-artist-lineup-equipment', 'EventController@postEditArtistLineupEquipment');

/* Promotions */

Route::post('/promotion/add', 'PromotionController@postCreatePromotion');

// Get promotion
Route::get('/promotion/{id}', 'PromotionController@getPromotion');

// Edit promotion
Route::get('/promotion/edit/{id}', 'PromotionController@getEditPromotion');
Route::post('/promotion/edit/{id}', 'PromotionController@postEditPromotion');

// Delete promotion
Route::get('/promotion/delete/{id}', 'PromotionController@getDeletePromotion');

/* Services */

// Request for performance
Route::post('/event/{id}/request-for-performance', 'ServiceController@postRequestForPerformance');
Route::post('/service/request-for-performance/confirm/{id}', 'ServiceController@postConfirmRequestForPerformance');
Route::get('/service/request-for-performance/reject/{id}', 'ServiceController@getRejectRequestForPerformance');
Route::get('/service/request-for-performance/cancel/{id}', 'ServiceController@getCancelRequestForPerformance');

// Request for service
Route::post('/service/request-for-service/confirm/{id}', 'ServiceController@postConfirmRequestForService');
Route::get('/service/request-for-service/reject/{id}', 'ServiceController@getRejectRequestForService');
Route::get('/service/request-for-service/cancel/{id}', 'ServiceController@getCancelRequestForService');

// Delete service (enable if needed)
//Route::get('/service/delete/{id}', 'ServiceController@getDeleteService');

/* Requests */

Route::get('/requests', 'EventController@getRequests');

/* Deactivate Account */
Route::get('/deactivate-account', 'UserController@getDeactivateAccount');

/* Profile Update */
Route::get('/profile/{id}', 'UserController@getPublicProfile');
Route::get('/profile', 'UserController@getUserProfile');
Route::get('/settings', 'UserController@getProfileSettings');
Route::post('/update-payment-info', 'UserController@postAjaxUpdatePaymentInfo');
Route::get('/update-profile', 'UserController@getUpdateProfile');
Route::post('/update-profile', 'UserController@postUpdateProfile');

/* Change Password */
Route::get('/change-password', 'UserController@getChangePassword');
Route::post('/change-password', 'UserController@postChangePassword');
Route::get('/validate-current-password', 'UserController@getValidateCurrentPassword');

/* Equipment */
Route::post('/add-equipment', 'EquipmentController@postAjaxAddEquipment');
// Route::get('/edit-equipment/{id}', ['uses' => 'EquipmentController@editEquipment']);
Route::post('/edit-equipment/{id}', ['uses' => 'EquipmentController@postEditEquipment']);
// Route::get('/delete-equipment/{id}', ['uses' => 'EquipmentController@deleteEquipment']);
Route::post('/delete-equipment/{id}', ['uses' => 'EquipmentController@postDeleteEquipment']);
Route::any('/delete-material/image','UserController@postAjaxDeleteMaterial');
Route::get('/get-ajax-equipment','EquipmentController@getAjaxEquipment');

/* Artist Materials */
Route::post('/update-materials', 'UserController@updateArtistMaterials');
Route::get('/delete-material/{id}', ['uses' => 'UserController@deleteMaterial']);
Route::post('/delete-material/{id}', ['uses' => 'UserController@postDeleteMaterial']);
Route::get('/get-all-materials','UserController@getAjaxMaterials');

/* Notifications */
Route::get('/notifications', 'NotificationController@index');
Route::post('/notifications', 'NotificationController@markAllAsRead');

/* Messages */

Route::get('/messages', 'MessageController@index');
Route::get('/message/conversation/{id}','MessageController@getMessageConversationWithUser');
Route::get('/message/get/{id}', 'MessageController@getMessage');
Route::get('/getconversation', 'MessageController@getConversation');
Route::get('/createmessage','MessageController@createMessage');
Route::get('/message/new','MessageController@getNewMessage');
Route::post('/group', 'MessageController@groupChat');
Route::get('/checkconversation','MessageController@checkConversation');
Route::get('messages/user/{id}','MessageController@getAjaxHeaderMessagesOfUser');
Route::get('messages/user/','MessageController@getAjaxHeaderMessages');
Route::get('messages/unread','MessageController@getAjaxNumberOfUnreadMessages');


/* Search */

Route::get('/search', 'SearchController@getSearch');
Route::get('/search-results', 'SearchController@getSearchResults');

/* Paypal */

// Paypal money transfer
// Route::get('/payment', 'PaypalController@getPayment');
// Route::post('/paypal-money-transfer', 'PaypalController@postPaypalMoneyTransfer');
// Route::get('/user-authorizes-money-transfer', 'PaypalController@getUserAuthorizesMoneyTransfer');
// Route::get('/user-cancels-money-transfer', 'PaypalController@getUserCancelsMoneyTransfer');

// Pay OurScene

Route::get('/pay', 'PaypalController@getPayOurscene');

Route::get('/pay-merchant/success', 'PaypalController@getPayMerchantSuccess');
Route::get('/pay-merchant/cancel', 'PaypalController@getPayMerchantCancel');
Route::get('/pay-merchant/error', 'PaypalController@getPayMerchantError');
Route::post('/pay-merchant', 'PaypalController@postPayToOurScene');
Route::post('/pay-merchant-credit', 'PaypalController@postPayToOurSceneCredit');
Route::post('/remove-payment-account','PaypalController@removePaymentAccount');

Route::get('/pay/hasvault','PaypalController@checkHasVault');
Route::get('/pay/getvault','PaypalController@ajaxGetVault');

Route::get('/autocomplete-users-with-paypal-accounts', 'PaypalController@getAutocompleteUsersWithPaypalAccount');

// Store credit card
Route::post('/store-credit-card', 'PaypalController@postStoreCreditCard');

/* Unused routes */


// Equipment Ajax

Route::get('/get-user-equiments','UserController@ajaxGetEquipment');
Route::post('/update-equipments','UserController@ajaxUpdateEquipment');
Route::post('/delete-equipments','UserController@ajaxDeleteEquipment');
Route::post('getequipment', 'EquipmentController@fetchEquipment');

Route::controller('_equipment', 'EquipmentController');
Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);

Route::get('/chatserver',function(){
	Artisan::queue("chat_server:serve");
	return "Running Chat Server";
});
