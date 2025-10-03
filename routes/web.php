<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['web','auth'])->group(function () {
	Route::get('/academy', function () {
		return view('humano-academy::academy.index');
	})->name('academy.index');
});
