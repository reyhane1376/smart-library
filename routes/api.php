<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::group([
    'prefix' => 'v1',
    'as' => 'v1.',
], function () {
    require('v1/api.php');
});

