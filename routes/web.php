<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ServiceRequestController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Pages principales
Route::get('/',           [PageController::class, 'home'])->name('home');
Route::get('/a-propos',   [PageController::class, 'about'])->name('about');
Route::get('/services',   [PageController::class, 'services'])->name('services');
Route::get('/formation',  [PageController::class, 'formation'])->name('formation');
Route::get('/projets', [PageController::class, 'caseStudies'])->name('case-studies');
Route::redirect('/etudes-de-cas', '/projets');
Route::get('/contact',    [PageController::class, 'contact'])->name('contact');

$profilePath = '/profile';

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/demande-service', [ServiceRequestController::class, 'create'])->name('requests.create');
    Route::post('/demande-service', [ServiceRequestController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('requests.store');
    // Accès contrôlé par ServiceRequestPolicy.
    Route::get('/projets/{serviceRequest}', [ProjectController::class, 'show'])
        ->whereNumber('serviceRequest')
        ->name('projects.show');
    Route::post('/projets/{serviceRequest}/messages', [ProjectController::class, 'sendMessage'])
        ->middleware('throttle:30,1')
        ->name('projects.messages.store');
    Route::post('/projets/{serviceRequest}/annuler', [ProjectController::class, 'cancel'])
        ->name('projects.cancel');
    Route::post('/projets/{serviceRequest}/documents', [ProjectController::class, 'addDocuments'])
        ->middleware('throttle:10,1')
        ->name('projects.documents.store');
    Route::delete('/projets/{serviceRequest}/documents/{documentIndex}', [ProjectController::class, 'removeDocument'])
        ->whereNumber('documentIndex')
        ->name('projects.documents.destroy');
    Route::get('/projets/{serviceRequest}/documents/{documentIndex}', [ProjectController::class, 'downloadDocument'])
        ->whereNumber('documentIndex')
        ->name('projects.documents.download');
    Route::get('/projets/{serviceRequest}/messages/{projectMessage}/piece-jointe', [ProjectController::class, 'downloadMessageAttachment'])
        ->name('projects.messages.attachment');
    // Accessible au client : hors du groupe admin/expert.
    Route::get('/jalons/{projectMilestone}/livrables/{deliverableIndex}', [ProjectController::class, 'downloadDeliverable'])
        ->whereNumber('deliverableIndex')
        ->name('projects.milestones.download');
});

// Formulaire de contact (limité contre le spam)
Route::post('/contact',      [ContactController::class, 'send'])
    ->middleware('throttle:5,1')
    ->name('contact.send');
Route::get('/contact/merci', [PageController::class, 'contactMerci'])->name('contact.merci');

Route::middleware('auth')->group(function () use ($profilePath) {
    Route::get($profilePath, [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch($profilePath, [ProfileController::class, 'update'])->name('profile.update');
    Route::delete($profilePath, [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/demandes', [\App\Http\Controllers\Admin\ServiceRequestController::class, 'index'])->name('requests.index');
    Route::patch('/demandes/{serviceRequest}', [\App\Http\Controllers\Admin\ServiceRequestController::class, 'update'])->name('requests.update');
    Route::get('/utilisateurs', [UserController::class, 'index'])->name('users.index');
    Route::get('/utilisateurs/nouveau', [UserController::class, 'create'])->name('users.create');
    Route::post('/utilisateurs', [UserController::class, 'store'])->name('users.store');
    Route::patch('/utilisateurs/{user}', [UserController::class, 'update'])->name('users.update');
    Route::post('/utilisateurs/{user}/suspendre', [UserController::class, 'toggleSuspension'])->name('users.suspension');
    Route::post('/utilisateurs/{user}/lien-mot-de-passe', [UserController::class, 'sendPasswordLink'])
        ->middleware('throttle:6,1')
        ->name('users.password-link');
});

Route::middleware(['auth', 'role:admin,expert'])->group(function () {
    Route::post('/projets/{serviceRequest}/jalons', [\App\Http\Controllers\Expert\ProjectMilestoneController::class, 'store'])->name('projects.milestones.store');
    Route::patch('/jalons/{projectMilestone}', [\App\Http\Controllers\Expert\ProjectMilestoneController::class, 'update'])->name('projects.milestones.update');
    Route::post('/jalons/{projectMilestone}/deplacer', [\App\Http\Controllers\Expert\ProjectMilestoneController::class, 'move'])->name('projects.milestones.move');
    Route::delete('/jalons/{projectMilestone}', [\App\Http\Controllers\Expert\ProjectMilestoneController::class, 'destroy'])->name('projects.milestones.destroy');
    Route::delete('/jalons/{projectMilestone}/livrables/{deliverableIndex}', [\App\Http\Controllers\Expert\ProjectMilestoneController::class, 'removeDeliverable'])
        ->whereNumber('deliverableIndex')
        ->name('projects.milestones.deliverables.destroy');
});

// Pages légales
Route::get('/mentions-legales', [PageController::class, 'mentionsLegales'])->name('mentions-legales');
Route::get('/politique-confidentialite', [PageController::class, 'politiqueConfidentialite'])->name('politique-confidentialite');

Route::get('/sitemap.xml', function () {
    $urls = [
        route('home'),
        route('about'),
        route('services'),
        route('case-studies'),
        route('formation'),
        route('contact'),
        route('mentions-legales'),
        route('politique-confidentialite'),
    ];

    return response()->view('sitemap', ['urls' => $urls])->header('Content-Type', 'application/xml');
})->name('sitemap');

require __DIR__.'/auth.php';
