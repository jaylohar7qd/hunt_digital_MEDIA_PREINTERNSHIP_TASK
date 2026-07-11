<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', [App\Http\Controllers\Caller\LoginController::class, 'index']);
Route::get('login', [App\Http\Controllers\Caller\LoginController::class, 'index'])->name('login');
Route::post('login', [App\Http\Controllers\Caller\LoginController::class, 'validateUser']);
Route::get('login/{driver}/start', [App\Http\Controllers\Caller\LoginController::class, 'redirectToProvider']);
Route::any('login/{driver}/callback', [App\Http\Controllers\Caller\LoginController::class, 'handleProviderCallback']);
Route::any('logout', [App\Http\Controllers\Caller\LoginController::class, 'Logout']);

Route::get('/html-page', function () {
	$templatePath = 'C:/Users/DELL/Downloads/template-html/template-html/html/vertical-menu-template/app-calendar.html';
	abort_unless(is_file($templatePath), 404);

	$html = file_get_contents($templatePath);
	$assetBase = asset('assets');

	$html = str_replace('../../assets', $assetBase, $html);

	return response($html);
})->name('html-page');

Route::middleware(['auth:web'])->group(function () {
});

