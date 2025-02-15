<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\RaffleController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\BalanceController;
use App\Http\Controllers\BalanceRechargeController;

// Página de bienvenida
Route::get('/', function () {
    return view('welcome');
});

// **CERRAR SESIÓN**
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::post('/logout/agent', [LoginController::class, 'agentLogout'])->name('agent.logout');
Route::post('/logout/player', [LoginController::class, 'playerLogout'])->name('player.logout');

// **REGISTRO Y LOGIN**
Route::prefix('register')->group(function () {
    Route::get('/agent', [AgentController::class, 'showRegistrationForm'])->name('agent.register.form');
    Route::post('/agent', [AgentController::class, 'register'])->name('agent.register');
    Route::get('/player', [PlayerController::class, 'showRegistrationForm'])->name('player.register.form');
    Route::post('/player', [PlayerController::class, 'register'])->name('player.register');
});

Route::prefix('login')->group(function () {
    Route::get('/agent', [LoginController::class, 'showAgentLoginForm'])->name('agent.login.form');
    Route::post('/agent', [LoginController::class, 'agentLogin'])->name('agent.login');
    Route::get('/player', [LoginController::class, 'showPlayerLoginForm'])->name('player.login.form');
    Route::post('/player', [LoginController::class, 'playerLogin'])->name('player.login');
});

// **RUTAS PARA AGENTES**
Route::middleware(['auth:agent'])->group(function () {
    Route::get('/agent/dashboard', [AgentController::class, 'dashboard'])->name('agent.dashboard');

    // **GESTIÓN DE SALAS (ROOMS)**
    Route::prefix('agent/rooms')->group(function () {
        Route::get('/', [RoomController::class, 'index'])->name('rooms.index');
        Route::get('/create', [RoomController::class, 'create'])->name('rooms.create');
        Route::post('/', [RoomController::class, 'store'])->name('rooms.store');
        Route::get('/{room}/edit', [RoomController::class, 'edit'])->name('rooms.edit');
        Route::put('/{room}', [RoomController::class, 'update'])->name('rooms.update');
        Route::delete('/{room}', [RoomController::class, 'destroy'])->name('rooms.destroy');
    });

    // **GESTIÓN DE SORTEOS (RAFFLES)**
    Route::prefix('agent/raffles')->group(function () {
        Route::get('/', [RaffleController::class, 'index'])->name('agent.raffles.index');
        Route::get('/create/{room_id}', [RaffleController::class, 'create'])->name('agent.raffles.create');
        Route::post('/store', [RaffleController::class, 'store'])->name('agent.raffles.store');
        Route::get('/{raffle}/edit', [RaffleController::class, 'edit'])->name('agent.raffles.edit');
        Route::put('/{raffle}', [RaffleController::class, 'update'])->name('agent.raffles.update');
        Route::get('/{raffle}/play', [RaffleController::class, 'play'])->name('agent.raffles.play');
        Route::post('/{raffle}/generate-number', [RaffleController::class, 'generateNumber'])->name('agent.raffles.generate');
        Route::get('/start/{raffle_id}', [RaffleController::class, 'startRaffle'])->name('agent.raffles.start');
        Route::get('/finish/{raffle_id}', [RaffleController::class, 'finishRaffle'])->name('agent.raffles.finish');
        Route::delete('/{raffle}', [RaffleController::class, 'destroy'])->name('agent.raffles.destroy');
    });

    // **GESTIÓN DE RECARGAS POR AGENTES**
    Route::prefix('agent/recharges')->group(function () {
        Route::get('/', [BalanceController::class, 'showPendingRecharges'])->name('agent.recharges.index');
        Route::post('/{recharge}/update', [BalanceController::class, 'updateRechargeStatus'])->name('agent.recharge.update');
    });
});

// **RUTAS PARA JUGADORES**
Route::middleware(['auth:player'])->group(function () {
    Route::get('/player/dashboard', [PlayerController::class, 'dashboard'])->name('player.dashboard');

    // **GESTIÓN DE RECARGAS POR JUGADORES**
    Route::prefix('player/recharges')->group(function () {
        Route::get('/create', [BalanceRechargeController::class, 'create'])->name('player.recharges.create');
        Route::post('/store', [BalanceRechargeController::class, 'store'])->name('player.recharges.store');
        Route::get('/', [BalanceController::class, 'showPlayerRecharges'])->name('player.recharges.index');
    });

    // **GESTIÓN DE CARTONES Y SALAS**
    Route::prefix('player')->group(function () {
        Route::post('/buy-card/{roomId}', [PlayerController::class, 'buyCard'])->name('player.buy.card');
        Route::get('/my-cards', [PlayerController::class, 'myCards'])->name('player.my-cards');
        Route::get('/my-cards/{room_id}', [PlayerController::class, 'myCardsByRoom'])->name('player.my-cards.by-room');
        Route::get('/room/{roomId}', [PlayerController::class, 'showRoom'])->name('player.sala');
        Route::post('/buy-cards', [RoomController::class, 'buyCards'])->name('player.buyCards');
    });

    // **SORTEOS PARA JUGADORES**
    Route::get('/player/room/{room}/sorteo', [PlayerController::class, 'viewSorteo'])->name('player.room.sorteo');
    Route::get('/player/raffles/{roomId}/numbers', [RaffleController::class, 'getGeneratedNumbers'])->name('player.raffle.numbers');
});

// **APIS PARA SORTEOS**
Route::prefix('api')->group(function () {
    Route::post('/raffles/{raffle}/generate-number', [RaffleController::class, 'generateNumber']);
    Route::get('/raffles/{raffle}/numbers', [RaffleController::class, 'getGeneratedNumbers']);
    Route::get('/raffle/{id}/check-winner', [RaffleController::class, 'checkWinner']);
});

// **VISTA EN VIVO DE SORTEOS**
Route::get('/agent/raffles/{raffle}/live', [RaffleController::class, 'liveView'])->name('agent.raffles.live');

// **DESACTIVAR LOGIN PREDETERMINADO DE LARAVEL**
Auth::routes(['login' => false]);
