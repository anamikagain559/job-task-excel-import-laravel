<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExcelImportController;

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
});


Route::get('import', [ExcelImportController::class, 'showImportForm']);
Route::post('import', [ExcelImportController::class, 'import'])->name('import');
Route::get('/download-failed-file', [ExcelImportController::class, 'downloadFailedFile'])->name('download-failed-file');
