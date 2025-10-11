<?php

use Idoneo\HumanoAcademy\Http\Controllers\AcademyController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/academy', [AcademyController::class, 'index'])->name('academy.index');
    Route::get('/academy/list', [AcademyController::class, 'index'])->name('academy.list');
    Route::get('/academy/course/{id?}', [AcademyController::class, 'courseDetails'])->name('academy.course.details');
});
