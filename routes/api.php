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


//Login and Register
Route::post('/login', 'Api\PassportController@login');
// Route::post('/register', 'Api\PassportController@register');

//Users
// Route::get('/users','Api\UserController@index');    //returns list of users
Route::post('/user/register','Api\UserController@store');   //creates new user


Route::group(['middleware' => 'auth:api'], function(){  //Authenticated only

    //Users
     //getprofile with auth
    Route::put('/user/updateprofile','Api\UserController@updateprofile'); // put profile with auth


    Route::get('/admin/user/getalluserinfo','Api\UserController@index'); // get all user info
    Route::delete('/admin/user/deleteuser/{id}','Api\UserController@destroy'); //delete user with id

    // Route::put('/users/{id}','Api\UserController@update'); //update user with id
    // Route::delete('/users/{id}','Api\USerController@destroy'); //delele user with id

    //Albums
    Route::post('/admin/gallery/createalbum','Api\AlbumController@store');   //create new album
    Route::put('/admin/gallery/updatealbum/{id}','Api\AlbumController@update'); //update album with id
    Route::delete('/admin/gallery/deletealbum/{id}','Api\AlbumController@destroy'); //delele album with id


    //Photos
    Route::post('/admin/gallery/createphotos','Api\PhotoController@store');   //upload new photo
    Route::put('/admin/gallery/updatephotos/{id}','Api\PhotoController@update'); //update photo with id
    Route::delete('/admin/gallery/deletephotos/{id}','Api\PhotoController@destroy'); //delele photo with id

    Route::post('/admin/testimonial/writereview', 'Api\TestimonialsController@store');
    Route::put('/admin/testimonial/editreview/{id}', 'Api\TestimonialsController@update');
    Route::delete('/admin/testimonial/deleteReview/{id}', 'Api\TestimonialsController@destroy');//dek


});


//Allows both guest and auth access
$middleware = ['api'];
if (\Request::header('Authorization'))
   $middleware = array_merge(['auth:api']);
Route::group(['middleware' => $middleware], function () {
    Route::get('/getallreview','Api\TestimonialsController@getallreview');
    Route::get('/getallalbums','Api\AlbumController@getallalbums');
    Route::get('/user/getprofile','Api\UserController@getprofile');

    Route::get('/review/{id}', 'Api\TestimonialsController@show');
    Route::get('/users/{id}', 'Api\UserController@show');   //with auth -> return all albums, guest-> return only public albums
    Route::get('/albums/{id}', 'Api\AlbumController@show'); //with auth -> return all photos, guest-> return only public photos
    Route::get('/photos/{id}', 'Api\PhotoController@show'); //with auth -> access private photo

});





