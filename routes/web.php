<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::resource('tasks', TaskController::class);
Route::get('notes/{note}/export', [\App\Http\Controllers\NoteController::class , 'export'])->name('notes.export');
Route::resource('notes', \App\Http\Controllers\NoteController::class);
