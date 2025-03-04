<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('api')->group(function () {

/* --------------------- AUTHENTICATION ----------------------------- */
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

/* --------------------- PROTECTED ----------------------------- */
Route::middleware('auth:sanctum')->group(function () {
    
    /* --------------------- BOOKS ----------------------------- */
    Route::apiResource('books', BookController::class);
    Route::get('books/{book}/available-copies', [BookController::class, 'availableCopies']);
    
    /* --------------------- Reservations ----------------------------- */
    Route::get('reservations', [ReservationController::class, 'index']);
    Route::post('reservations', [ReservationController::class, 'store']);
    Route::put('reservations/{reservation}/cancel', [ReservationController::class, 'cancel']);
    Route::get('copies/{copy}/reservation-queue', [ReservationController::class, 'queue']);
    
    // Borrowings
    Route::get('borrowings', [BorrowingController::class, 'index']);
    Route::post('borrowings', [BorrowingController::class, 'store']);
    Route::put('borrowings/{borrowing}/return', [BorrowingController::class, 'return']);
    Route::put('borrowings/{borrowing}/extend', [BorrowingController::class, 'extend']);
});
});