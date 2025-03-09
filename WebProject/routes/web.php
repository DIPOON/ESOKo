<?php

use App\Http\Controllers\DownloadController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TranslationController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::get('/translate', [TranslationController::class, 'randomShow'])->name('translate');
Route::get('/translate-sub', [TranslationController::class, 'getSub']);
Route::post('/translate', [TranslationController::class, 'submit']);

Route::get('/search', [SearchController::class, 'get'])->name('search');
Route::get('/search-text', [SearchController::class, 'getByText'])->name('search.text');

Route::get('/download', [DownloadController::class, 'get'])->name('download');

require __DIR__.'/auth.php';
