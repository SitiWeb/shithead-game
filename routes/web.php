<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;
use App\Http\Livewire\MyComponent;
use App\Livewire\PlayerCards;

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware(['auth'])->group(function () {
    // Display a list of available games (lobby page)
    Route::get('/games', [App\Http\Controllers\GameController::class,'index'])->name('games.index');

    // Create a new game
    Route::post('/games',  [App\Http\Controllers\GameController::class,'createGame'])->name('games.create');

    // Join an existing game
    Route::get('/games/{game}/data',  [App\Http\Controllers\GameController::class,'gameData'])->name('games.data');


    // Join an existing game
    Route::get('/games/{game}/join',  [App\Http\Controllers\GameController::class,'joinGame'])->name('games.join');

    // View the game Lobby
    Route::get('/games/{game}/lobby', [App\Http\Controllers\GameController::class,'showLobby'])->name('games.lobby');

    // Start the game board
    Route::get('/games/{game}/start', [App\Http\Controllers\GameController::class,'startGame'])->name('games.start');

    // Start the game board
    Route::post('/games/{game}/switch', [App\Http\Controllers\GameController::class,'switch'])->name('games.switch');

    // Start the game board
    Route::post('/games/{game}/action', [App\Http\Controllers\GameController::class,'action'])->name('games.action');

    // View the game board
    Route::get('/games/{game}', [App\Http\Controllers\GameController::class,'showBoard'])->name('games.show');

    // Play a card
    Route::post('/games/{game}/play', 'GameController@play')->name('games.play');
    Route::get('/player-cards', PlayerCards::class);
    // Additional routes can be added here as needed
});
