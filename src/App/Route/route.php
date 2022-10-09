<?php

use Snow\StuWeb\Support\Facades\Route;

Route::get('index', [\Snow\StuWeb\App\Controller\Index::class, 'index']);
Route::group('order', function () {
    Route::get('create', function () {
        Route::get('list', function () {
            return 'list 1';
        });
    });

    Route::get('index', [\Snow\StuWeb\App\Controller\Order::class, 'index'])->middleware(\Snow\StuWeb\App\Middlewares\TokenMiddleware::class);
    Route::post('save', [\Snow\StuWeb\App\Controller\Order::class, 'save']);
    Route::put('update', [\Snow\StuWeb\App\Controller\Order::class, 'update']);
    Route::put('delete', [\Snow\StuWeb\App\Controller\Order::class, 'delete']);
})->middleware(\Snow\StuWeb\App\Middlewares\AuthMiddleware::class);