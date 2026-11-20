<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\PetitionController;
use App\Http\Middleware\AdminBasicAuth;
use Illuminate\Support\Facades\Route;

Route::get('/', [PetitionController::class, 'index'])->name('petition.index');
Route::post('/signer', [PetitionController::class, 'store'])->middleware('throttle:6,1')->name('petition.store');
Route::get('/merci', [PetitionController::class, 'thanks'])->name('petition.thanks');
Route::get('/confirmer/{signataire}', [PetitionController::class, 'verify'])->middleware('signed')->name('petition.verify');
Route::get('/signataires', [PetitionController::class, 'signatures'])->name('petition.signatures');

Route::middleware(AdminBasicAuth::class)->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');
    Route::get('/export.csv', [AdminController::class, 'export'])->name('export');
    Route::delete('/signatures/{signature}', [AdminController::class, 'destroy'])->name('destroy');
});
