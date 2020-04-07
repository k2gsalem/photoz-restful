<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Retruns details of Current authorized user
Route::get('/details', 'Api\PassportController@details')->middleware('auth:api');

Route::get('/config-clear', function() {
    $status = Artisan::call('config:clear');
    return '<h1>Configurations cleared</h1>';
});
Route::get('/cache-clear', function() {
    $status = Artisan::call('cache:clear');
    return '<h1>Cache cleared</h1>';
});
Route::get('/config-cache', function() {
    $status = Artisan::call('config:cache');
    return '<h1>Configurations cache cleared</h1>';
});

Route::get('/storage-link', function() {
    $status= exec('cd .. && dir && rm public/storage');
    $status = Artisan::call('storage:link');
    return $status;
});

Route::get('/passport-install', function() {
   // $status= exec('cd .. && dir && rm public/storage');
    $status = Artisan::call('passport:install');
    return $status;
});
Route::get('/migrate-fresh', function() {
 //   $status= exec('cd .. && dir && rm public/storage');
    $status = Artisan::call('migrate:fresh');
    return $status;
});
Route::get('/composer-install', function() {
     $status= exec('cd .. && dir && composer install');
     //$status = Artisan::call('passport:install');
     return $status;
 });
//Login and Register
Route::post('/login', 'Api\PassportController@login');
// Route::post('/register', 'Api\PassportController@register');

//Users
// Route::get('/users','Api\UserController@index');    //returns list of users
Route::post('/user/register','Api\UserController@store');   //creates new user


Route::group(['middleware' => 'auth:api'], function(){  //Authenticated only

    //Users
     //getprofile with auth
    Route::put('/user/updateProfile','Api\UserController@updateprofile'); // put profile with auth


    Route::get('/admin/user/getAllUserInfo','Api\UserController@index'); // get all user info
    Route::delete('/admin/user/deleteUser/{id}','Api\UserController@destroy'); //delete user with id

    // Route::put('/users/{id}','Api\UserController@update'); //update user with id
    // Route::delete('/users/{id}','Api\USerController@destroy'); //delele user with id

    //Albums
    Route::post('/admin/gallery/createAlbum','Api\AlbumController@store');   //create new album
    Route::put('/admin/gallery/updateAlbum/{id}','Api\AlbumController@update'); //update album with id
    Route::delete('/admin/gallery/deleteAlbum/{id}','Api\AlbumController@destroy'); //delele album with id


    //Photos
    Route::post('/admin/gallery/createPhotos','Api\PhotoController@store');   //upload new photo
    Route::put('/admin/gallery/updatePhotos/{id}','Api\PhotoController@update'); //update photo with id
    Route::delete('/admin/gallery/deletePhotos/{id}','Api\PhotoController@destroy'); //delele photo with id

    Route::post('/admin/gallery/createVideos','Api\VideoController@store');   //upload new photo
    Route::put('/admin/gallery/updateVideos/{id}','Api\VideoController@update'); //update photo with id
    Route::delete('/admin/gallery/deleteVideos/{id}','Api\VideoController@destroy'); //delele photo with id

    Route::post('/admin/testimonial/writeReview', 'Api\TestimonialsController@store');
    Route::put('/admin/testimonial/editReview/{id}', 'Api\TestimonialsController@update');
    Route::delete('/admin/testimonial/deleteReview/{id}', 'Api\TestimonialsController@destroy');//dek


});


//Allows both guest and auth access
$middleware = ['api'];
if (\Request::header('Authorization'))
   $middleware = array_merge(['auth:api']);
Route::group(['middleware' => $middleware], function () {
    Route::get('/getAllReview','Api\TestimonialsController@getallreview');
    Route::get('/getAllAlbums','Api\AlbumController@getallalbums');
    Route::get('/user/getProfile','Api\UserController@getprofile');

    Route::get('/review/{id}', 'Api\TestimonialsController@show');
    Route::get('/users/{id}', 'Api\UserController@show');   //with auth -> return all albums, guest-> return only public albums
    Route::get('/albums/{id}', 'Api\AlbumController@show'); //with auth -> return all photos, guest-> return only public photos
    Route::get('/photos/{id}', 'Api\PhotoController@show'); //with auth -> access private photo
    Route::get('/videos/{id}', 'Api\VideoController@show');
});





