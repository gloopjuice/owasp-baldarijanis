<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;
Route::post('/register', [ApiController::class, 'register'])->middleware('throttle:10,1');
Route::post('/login', [ApiController::class, 'login'])->middleware('throttle:10,1');


// Protected routes
Route::group([
    "middleware" => "auth:api"
], function () {
    Route::get('/profile', [ApiController::class, 'profile']);
    Route::get('/logout', [ApiController::class, 'logout']);

    Route::get('/deleteUser', [ApiController::class, 'deleteUser']);
    Route::get('/getUserProfile', [ApiController::class, 'getUserProfile']);
    Route::post('/updateProfile', [ApiController::class, 'updateProfile']);
    Route::delete('/deleteProfile', [ApiController::class, 'deleteProfile']);

    Route::post('/createForumPost', [ApiController::class, 'createForumPost']);
    Route::get('/getForumPost', [ApiController::class, 'getForumPost']);
    Route::get('/getAllForumPosts', [ApiController::class, 'getAllForumPosts']);
    Route::get('/post/{id}', [ApiController::class,'show']);

    Route::middleware('auth:api')->delete('/deletePost/{id}', [ApiController::class, 'deletePost']);
    Route::post('/editForumPost/{id}', [ApiController::class, 'editForumPost']);

    Route::post('/uploadFile', [ApiController::class, 'uploadFile']);
    Route::post('/deleteFile', [ApiController::class, 'deleteFile']);

    Route::get('/getAllUsers', [ApiController::class, 'getAllUsers'])->middleware('auth:api');

    Route::delete('/deleteUserProfile/{id}', [ApiController::class, 'deleteUserProfile']);
    Route::put('/updateUserProfile/{id}', [ApiController::class, 'editUserProfile']);  // Ensure this line is correct

    Route::post('/createComment', [ApiController::class, 'createComment']);
    Route::get('/getComments', [ApiController::class, 'getComments']);
    Route::put('/updateComment/{id}', [ApiController::class, 'updateComment']);
    Route::delete('/deleteComment/{id}', [ApiController::class, 'deleteComment']);
});
