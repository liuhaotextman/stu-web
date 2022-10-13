<?php

use Snow\StuWeb\Support\Facades\Route;

Route::get('index', [\App\Controller\Index::class, 'index']);
Route::group('order', function () {
    Route::get('create', function () {
        Route::get('list', function () {
            return 'list 1';
        });
    });

    Route::get('index', [\App\Controller\Order::class, 'index'])->middleware(\App\Middlewares\TokenMiddleware::class);
    Route::post('save', [\App\Controller\Order::class, 'save']);
    Route::put('update', [\App\Controller\Order::class, 'update']);
    Route::put('delete', [\App\Controller\Order::class, 'delete']);
})->middleware(\App\Middlewares\AuthMiddleware::class);