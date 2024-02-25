<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
// route::middleware('myMiddleware')->get('/middleware',function(){
//     return view('welcome');
// });
Route::get('/', function () {
    return view('welcome');
});
route::group(['middleware'=>'myGust'],function(){

    Route::get('/login', function () {
        return view('login');
    });
    route::post('/login', function (Request $request) {
        $name = $request->name;
        $password = $request->password;
        $result = DB::select('select * from pregisters where pemail = ? and password = ?', [$name, $password]);
        if ($result) {
            $user = $result[0];
            session(['login' => 'true','id'=>$user->id, 'name' => $user]);
            return redirect('/profile');
        } else {
            return redirect('/login')->with(['message' => 'wrong email or password'])->withInput();
        }
    });
});

route::group(['middleware' => 'myAuth'], function () {
    route::get('/profile', function () {

        return view('profile');
    });
    route::get('/logout', function () {
        session()->flush();
        return redirect('/login');
    });
});
// Route::get('/params', function (Request $request) {
//     $name = $request->query('name');
//     return $name;
// });

