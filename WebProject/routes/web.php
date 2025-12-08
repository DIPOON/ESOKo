<?php

use App\Http\Controllers\DownloadController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TermController;
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

Route::get('/dashboard', [TranslationController::class, 'getLog'])->middleware(['auth'])->name('dashboard');

Route::get('/translate', [TranslationController::class, 'randomShow'])->name('translate');
Route::get('/translate-sub', [TranslationController::class, 'getSub']);
Route::post('/translate', [TranslationController::class, 'submit']);

Route::get('/search', [SearchController::class, 'get'])->name('search');
Route::get('/search-text', [SearchController::class, 'getByText'])->name('search.text');
Route::get('/search-detail', [SearchController::class, 'goTranslate']);

Route::get('/download', [DownloadController::class, 'get'])->name('download');

Route::get('/glossary', [TermController::class, 'index'])->name('glossary.index');
Route::post('/glossary', [TermController::class, 'store'])->name('glossary.store');
Route::put('/glossary/{term}', [TermController::class, 'update'])->name('glossary.update');
Route::delete('/glossary/{term}', [TermController::class, 'destroy'])->name('glossary.destroy');

require __DIR__.'/auth.php';
