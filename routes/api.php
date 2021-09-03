<?php

use Illuminate\Support\Facades\Route;
use Marshmallow\Zipcode\Http\ZipcodePrefillController;

Route::post('/get-address-information', ZipcodePrefillController::class);
