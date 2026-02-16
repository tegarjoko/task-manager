<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::resource('tasks', TaskController::class);
Route::get('/ops', [\App\Http\Controllers\OpsController::class , 'index'])->name('ops.index');
Route::post('/ops/update-date', [\App\Http\Controllers\OpsController::class , 'updateDate'])->name('ops.update-date');
Route::get('/ops/export', [\App\Http\Controllers\OpsController::class , 'export'])->name('ops.export');

Route::get('notes/{note}/export', [\App\Http\Controllers\NoteController::class , 'export'])->name('notes.export');
Route::resource('notes', \App\Http\Controllers\NoteController::class);
