<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DutyTaxReportController;

Route::get('/', function () {
    return view('welcome');
});

// Route untuk laporan PDF DutyTax
Route::get('/duty-tax/report/{year}/{month}', [DutyTaxReportController::class, 'print'])
    ->name('duty-tax.report');
