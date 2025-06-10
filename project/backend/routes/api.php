<?php

use App\Http\Controllers\Auth\ChatConrtoller;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\OrderOnServiceController;
use App\Http\Controllers\Auth\OrdersController;
use App\Http\Controllers\Auth\PersonalController;
use App\Http\Controllers\Auth\RoomsController;
use App\Http\Controllers\Auth\ServiceController;
use App\Http\Controllers\Auth\TypeRoomController;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//API Регистрация авторизация выход
Route::middleware('Auth')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware('Auth')->get('/userRoom', function (Request $request) {
    if (is_null($request->user()->getRoom())) {
        return response()->json('За вами не прикреплён номер', 403);
    }
    return $request->user()->getRoom();
});
Route::middleware('Auth')->get('/userOrder', function (Request $request) {
    if (is_null($request->user()->getOrder())) {
        return response()->json('У вас нету активной брони', 403);
    }
    return $request->user()->getOrder();
});

Route::post('/login', [LoginController::class, 'login'])->middleware('Guest');
Route::post('/logout', [LoginController::class, 'logout'])->middleware('Auth');
Route::post('/registration', [LoginController::class, 'registration'])->middleware('Guest');

//API Ресурсные записи
Route::middleware('Auth')->get('/orderInfo', function (Request $request) {
    return Order::with('user')->where('id', $request->id)->first();
});
Route::middleware('Auth')->get('/joinChat', [ChatConrtoller::class, 'join']);
Route::middleware('Auth')->get('/newChats', [ChatConrtoller::class, 'getNewChats']);
Route::middleware('Auth')->post('/sendMessage', [ChatConrtoller::class, 'sendMessage']);
Route::middleware('Auth')->post('/sendMessageMenager', [ChatConrtoller::class, 'sendMessageFromMenager']);
Route::middleware('Auth')->get('/getMessages', [ChatConrtoller::class, 'getMessages']);
Route::resource('/room', RoomsController::class)->middleware('Auth');
Route::resource('/personal', PersonalController::class)->middleware('Admin');
Route::resource('/service', ServiceController::class)->middleware('Auth');
Route::resource('/order', OrdersController::class)->middleware('Auth');
Route::resource('/orderService', OrderOnServiceController::class)->middleware('Auth');
Route::resource('/typeRooms', TypeRoomController::class)->middleware('Auth');

//API Записи
Route::get('/getTypeRooms', [TypeRoomController::class, 'index']);
Route::get('/getServices', [ServiceController::class, 'getServices']);

//Отчёты
Route::get('/getReportOrders', [OrdersController::class, 'createReport'])->middleware('Admin');
Route::get('/getReportServiceOrders', [OrderOnServiceController::class, 'createReport'])->middleware('Admin');
