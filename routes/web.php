<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ContactController;

// ──────────────────────────────────────────
//  Pages principales
// ──────────────────────────────────────────────
Route::get('/',           [PageController::class, 'home'])->name('home');
Route::get('/a-propos',   [PageController::class, 'about'])->name('about');
Route::get('/services',   [PageController::class, 'services'])->name('services');
Route::get('/formation',  [PageController::class, 'formation'])->name('formation');
Route::get('/etudes-de-cas', [PageController::class, 'caseStudies'])->name('case-studies');
Route::get('/contact',    [PageController::class, 'contact'])->name('contact');

// Formulaire de contact
Route::post('/contact',       [ContactController::class, 'send'])->name('contact.send');
Route::get('/contact/merci',  [PageController::class, 'contactMerci'])->name('contact.merci');

// ──────────────────────────────────────────────
//  Pages légales
// ──────────────────────────────────────────────
Route::get('/mentions-legales',              [PageController::class, 'mentionsLegales'])->name('mentions-legales');
Route::get('/politique-confidentialite',     [PageController::class, 'politiqueConfidentialite'])->name('politique-confidentialite');
