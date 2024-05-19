<?php

use App\Helpers\MyTokenManager;
use App\Http\Controllers\api\BlogController;
use App\Http\Controllers\api\PillController;
use App\Http\Controllers\api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

// Route::get('/tests',function(){
//     return [
//         'message'=>'my name is mahmoud',
//     ];
// });


// Route::post('/login', function (Request $request) {
//     $email = $request->email;
//     $password = $request->password;
//     $result = DB::select('select * from users where email=? and password=?', [$email, Hash::make($password)]);
//     dd($result);
//     if ($result) {
//         $user = $result[0];
//         $token = MyTokenManager::CreateToken($user->id);
//         return [
//             'message' => 'logged in successfully',
//             'token' => $token,
//         ];
//     } else {
//         return [
//             'error' => 'wrong email or password',
//         ];
//     }
// });
Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);

Route::group(['middleware' => 'MyAuthApi'], function () {


    //---------------------------------start profile--------------------------------------------

    Route::post('/userphoto', [UserController::class, 'uploadUserPhoto']);
    Route::post('/userpersonalinformation', [UserController::class, 'updatePersonalInformation']);
    Route::get('/profile', [UserController::class, 'profile']);

    //----------------------------------------end profile---------------------------------------

    //------------------------------------start Blog----------------------------------------------

    Route::get('/blog/index', [BlogController::class, 'index']);
    Route::get('/blog/search', [BlogController::class, 'search']);
    Route::get('/blog/show', [BlogController::class, 'show']);

    //------------------------------------end Blog---------------------------------------

    //--------------------------------------start detection -----------------------------------------

    Route::post('/detection', [PillController::class, 'detection']);
    Route::get('/detection/dosage', [PillController::class, 'pillDetectionDosageData']);
    Route::get('/detection/contraindiacation', [PillController::class, 'pillDetectionContraindiacationsData']);
    Route::get('/detection/sideeffect', [PillController::class, 'pillDetectionSideEffectsData']);
    Route::get('/detection/history', [PillController::class, 'PillDetectionUserHistory']);
    Route::get('/detection/history/show/{id}', [PillController::class, 'ShowPillDetectionUserHistory']);
    Route::delete('detection/history/delete/{id}', [PillController::class, 'DeletePillDetectionHistory']);




    //---------------------------------------end detection---------------------------------------------

    //---------------------------------------start Interaction--------------------------------------------------

    Route::get('/interaction/index', [PillController::class, 'interactionIndex']);
    Route::get('/interaction', [PillController::class, 'interaction']);
    Route::post('/imageInteraction', [PillController::class, 'imageInteraction']);
    Route::get('/interaction/history', [PillController::class, 'PillInteractionUserHistory']);
    Route::get('/interaction/history/show/{id}', [PillController::class, 'ShowPillInteractionUserHistory']);
    Route::delete('interaction/history/delete/{id}', [PillController::class, 'DeletePillInteractionHistory']);


    //----------------------------------------End Interaction----------------------------------------------------
    Route::get('/logout', [UserController::class, 'logout']);
});