<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RaffleController;

Route::post('/raffles/{raffleId}/generate-number', [RaffleController::class, 'generateNumber']);
Route::get('/raffles/{raffleId}/numbers', [RaffleController::class, 'getGeneratedNumbers']);
Route::get('/raffle/{id}/check-winner', [RaffleController::class, 'checkWinner']);
Route::get('/check-winner', [RaffleController::class, 'checkWinnerAPI']);
Route::post('/raffles/{id}/generate-number', [RaffleController::class, 'generateNumber']);

