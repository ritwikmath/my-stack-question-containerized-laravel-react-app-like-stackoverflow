<?php

use App\Http\Controllers\AnswerController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\TagController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::any('login', [\App\Http\Controllers\AuthController::class, 'login'])->name('login');

Route::middleware(['auth:api'])->group(function () {
    Route::resource('questions', QuestionController::class)->except(['create', 'edit']);
    Route::resource('tags', TagController::class)->except(['create', 'edit', 'update', 'delete']);
    Route::resource('answers', AnswerController::class)->except(['index', 'show', 'create', 'edit']);
    Route::get('tags/search/{term?}', [TagController::class, 'search'])->name('tags.search');
    Route::get('answers/{question_id}', [AnswerController::class, 'index'])->name('answers.index');
    Route::post('tags/{tag}/{question_id}', [TagController::class, 'remove'])->name('tags.remove');
});
