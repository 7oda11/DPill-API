<?php

use App\Helpers\MyTokenManager;
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
Route::post('/login',[UserController::class,'login']);

Route::post('/register', [UserController::class, 'register']);

Route::group(['middleware' => 'MyAuthApi'], function () {
    Route::get('/detection', [PillController::class, 'pillDetectionData']);
    Route::post('/userphoto', [UserController::class, 'uploadUserPhoto']);
    Route::post('/userpersonalinformation', [UserController::class, 'updatePersonalInformation']);
    Route::get('/profile',[UserController::class, 'profile']);
    Route::get('/logout', [UserController::class, 'logout']);
});
