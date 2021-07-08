<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\BotTokenController;
use App\Http\Controllers\CompleteController;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// Route::get('/',function(){
//     $url = $request->fullUrlWithQuery(['bar' => 'baz']);
//     return view('index',['url',$url]);
// });
Route::get('/',[IndexController::class,'loadView']);
Route::post('/',[IndexController::class,'start']);
Route::post('/logout',[IndexController::class,'logout']);

Route::get('/bottoken',[BotTokenController::class,'loadView']);
Route::post('/bottoken',[BotTokenController::class,'setWebhook']);

Route::get('/complete',[CompleteController::class,'loadView']);

/**
 * @hideFromAPIDocumentation
 */
Route::fallback(function () {
    //Send to 404 or whatever here.
    return abort(404);
});