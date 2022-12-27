<?php

use Illuminate\Support\Facades\Route;
use Tomodo531\FilterableFilters\Http\Controllers\OptionsController;

Route::post('/options', [OptionsController::class, 'options']);
