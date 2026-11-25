<?php

use App\Http\Controllers\Account\AddressController;
use App\Http\Controllers\Account\OrderController as AccountOrderController;
use App\Http\Controllers\Account\ReviewController as AccountReviewController;
use App\Http\Controllers\Admin\ServiceRequestController as AdminRequestController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Expert\ProjectMilestoneController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectListController;
use App\Http\Controllers\ServiceRequestController;
use App\Http\Controllers\Shop\CartController;
use App\Http\Controllers\Shop\CatalogController;
use App\Http\Controllers\Shop\CheckoutController;
use App\Http\Controllers\Shop\OrderController as ShopOrderController;
use App\Http\Controllers\Shop\PaymentController;
use App\Http\Controllers\ShopAdmin;
use App\Models\Shop\Product;
use Illuminate\Support\Facades\Route;

// Pages principales
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/a-propos', [PageController::class, 'about'])->name('about');
Route::get('/services', [PageController::class, 'services'])->name('services');
Route::get('/formation', [PageController::class, 'formation'])->name('formation');
Route::get('/projets', [PageController::class, 'caseStudies'])->name('case-studies');
Route::redirect('/etudes-de-cas', '/projets');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [ContactController::class, 'send'])->middleware('throttle:5,1')->name('contact.send');
Route::get('/contact/merci', [PageController::class, 'contactMerci'])->name('contact.merci');

// Pages légales
Route::get('/mentions-legales', [PageController::class, 'mentionsLegales'])->name('mentions-legales');
Route::get('/politique-confidentialite', [PageController::class, 'politiqueConfidentialite'])->name('politique-confidentialite');
Route::get('/conditions-generales-de-vente', [PageController::class, 'cgv'])->name('cgv');

// Boutique
Route::get('/boutique', [CatalogController::class, 'index'])->name('shop.index');
Route::get('/boutique/produit/{product}', [CatalogController::class, 'show'])->name('shop.product');
Route::get('/panier', [CartController::class, 'show'])->name('shop.cart');
Route::middleware('throttle:60,1')->group(function () {
    Route::post('/panier', [CartController::class, 'add'])->name('shop.cart.add');
    Route::patch('/panier/{item}', [CartController::class, 'update'])->name('shop.cart.update');
    Route::delete('/panier/{item}', [CartController::class, 'remove'])->name('shop.cart.remove');
    Route::post('/panier/code-promo', [CartController::class, 'applyCoupon'])->middleware('throttle:10,1')->name('shop.cart.coupon');
    Route::delete('/panier/code-promo', [CartController::class, 'removeCoupon'])->name('shop.cart.coupon.remove');
});
Route::get('/commande', [CheckoutController::class, 'show'])->name('shop.checkout');
Route::post('/commande', [CheckoutController::class, 'store'])->middleware('throttle:10,1')->name('shop.checkout.store');
Route::get('/commandes/{order}', [ShopOrderController::class, 'show'])->name('shop.orders.show');
Route::post('/commandes/{order}/payer', [ShopOrderController::class, 'pay'])->middleware('throttle:10,1')->name('shop.orders.pay');
Route::get('/commandes/{order}/facture', [ShopOrderController::class, 'invoice'])->name('shop.orders.invoice');
Route::get('/paiement/{payment}/retour', [PaymentController::class, 'back'])->name('shop.payment.return');
Route::get('/paiement/test/{payment}', [PaymentController::class, 'simulate'])->middleware('signed')->name('shop.payment.simulate');
Route::post('/paiement/test/{payment}', [PaymentController::class, 'confirmSimulation'])->middleware('signed')->name('shop.payment.simulate.confirm');
Route::post('/webhooks/paiement/{provider}', [PaymentController::class, 'webhook'])->middleware('throttle:120,1')->name('shop.webhook');

// Espace connecté
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/mon-espace/projets', ProjectListController::class)->name('projects.index');

    Route::middleware('role:client,admin')->group(function () {
        Route::get('/demande-service', [ServiceRequestController::class, 'create'])->name('requests.create');
        Route::post('/demande-service', [ServiceRequestController::class, 'store'])->middleware('throttle:10,1')->name('requests.store');
    });

    // Accès contrôlé par ServiceRequestPolicy.
    Route::get('/projets/{serviceRequest}', [ProjectController::class, 'show'])->whereNumber('serviceRequest')->name('projects.show');
    Route::post('/projets/{serviceRequest}/messages', [ProjectController::class, 'sendMessage'])->middleware('throttle:30,1')->name('projects.messages.store');
    Route::post('/projets/{serviceRequest}/annuler', [ProjectController::class, 'cancel'])->name('projects.cancel');
    Route::post('/projets/{serviceRequest}/documents', [ProjectController::class, 'addDocuments'])->middleware('throttle:10,1')->name('projects.documents.store');
    Route::delete('/projets/{serviceRequest}/documents/{documentIndex}', [ProjectController::class, 'removeDocument'])->whereNumber('documentIndex')->name('projects.documents.destroy');
    Route::get('/projets/{serviceRequest}/documents/{documentIndex}', [ProjectController::class, 'downloadDocument'])->whereNumber('documentIndex')->name('projects.documents.download');
    Route::get('/projets/{serviceRequest}/messages/{projectMessage}/piece-jointe', [ProjectController::class, 'downloadMessageAttachment'])->name('projects.messages.attachment');
    Route::get('/jalons/{projectMilestone}/livrables/{deliverableIndex}', [ProjectController::class, 'downloadDeliverable'])->whereNumber('deliverableIndex')->name('projects.milestones.download');

    // Commandes, adresses et avis du client
    Route::get('/mon-espace/commandes', [AccountOrderController::class, 'index'])->name('account.orders.index');
    Route::get('/mon-espace/commandes/{order}', [AccountOrderController::class, 'show'])->name('account.orders.show');
    Route::post('/mon-espace/commandes/{order}/recommander', [AccountOrderController::class, 'reorder'])->name('account.orders.reorder');
    Route::get('/mon-espace/adresses', [AddressController::class, 'index'])->name('account.addresses.index');
    Route::post('/mon-espace/adresses', [AddressController::class, 'store'])->name('account.addresses.store');
    Route::patch('/mon-espace/adresses/{address}', [AddressController::class, 'update'])->name('account.addresses.update');
    Route::delete('/mon-espace/adresses/{address}', [AddressController::class, 'destroy'])->name('account.addresses.destroy');
    Route::post('/mon-espace/avis', [AccountReviewController::class, 'store'])->middleware('throttle:10,1')->name('account.reviews.store');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin,expert'])->group(function () {
    Route::post('/projets/{serviceRequest}/jalons', [ProjectMilestoneController::class, 'store'])->name('projects.milestones.store');
    Route::patch('/jalons/{projectMilestone}', [ProjectMilestoneController::class, 'update'])->name('projects.milestones.update');
    Route::post('/jalons/{projectMilestone}/deplacer', [ProjectMilestoneController::class, 'move'])->name('projects.milestones.move');
    Route::delete('/jalons/{projectMilestone}', [ProjectMilestoneController::class, 'destroy'])->name('projects.milestones.destroy');
    Route::delete('/jalons/{projectMilestone}/livrables/{deliverableIndex}', [ProjectMilestoneController::class, 'removeDeliverable'])->whereNumber('deliverableIndex')->name('projects.milestones.deliverables.destroy');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/demandes', [AdminRequestController::class, 'index'])->name('requests.index');
    Route::patch('/demandes/{serviceRequest}', [AdminRequestController::class, 'update'])->name('requests.update');
    Route::get('/utilisateurs', [UserController::class, 'index'])->name('users.index');
    Route::get('/utilisateurs/nouveau', [UserController::class, 'create'])->name('users.create');
    Route::post('/utilisateurs', [UserController::class, 'store'])->name('users.store');
    Route::patch('/utilisateurs/{user}', [UserController::class, 'update'])->name('users.update');
    Route::post('/utilisateurs/{user}/suspendre', [UserController::class, 'toggleSuspension'])->name('users.suspension');
    Route::post('/utilisateurs/{user}/lien-mot-de-passe', [UserController::class, 'sendPasswordLink'])->middleware('throttle:6,1')->name('users.password-link');
    Route::get('/reglages', [SettingsController::class, 'edit'])->name('settings.edit');
    Route::put('/reglages', [SettingsController::class, 'update'])->name('settings.update');
});

// Gestion de la boutique : administrateurs et gestionnaires
Route::middleware(['auth', 'role:admin,manager'])->prefix('gestion/boutique')->name('shop-admin.')->group(function () {
    Route::get('/', ShopAdmin\DashboardController::class)->name('dashboard');

    Route::get('/commandes', [ShopAdmin\OrderController::class, 'index'])->name('orders.index');
    Route::get('/commandes/{order}', [ShopAdmin\OrderController::class, 'show'])->name('orders.show');
    Route::post('/commandes/{order}/avancer', [ShopAdmin\OrderController::class, 'advance'])->name('orders.advance');
    Route::post('/commandes/{order}/encaisser', [ShopAdmin\OrderController::class, 'collect'])->name('orders.collect');
    Route::post('/commandes/{order}/annuler', [ShopAdmin\OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/commandes/{order}/rembourser', [ShopAdmin\OrderController::class, 'refund'])->name('orders.refund');
    Route::post('/commandes/{order}/note', [ShopAdmin\OrderController::class, 'note'])->name('orders.note');

    Route::get('/produits', [ShopAdmin\ProductController::class, 'index'])->name('products.index');
    Route::get('/produits/nouveau', [ShopAdmin\ProductController::class, 'create'])->name('products.create');
    Route::post('/produits', [ShopAdmin\ProductController::class, 'store'])->name('products.store');
    Route::get('/produits/{product}', [ShopAdmin\ProductController::class, 'edit'])->name('products.edit');
    Route::put('/produits/{product}', [ShopAdmin\ProductController::class, 'update'])->name('products.update');
    Route::delete('/produits/{product}', [ShopAdmin\ProductController::class, 'destroy'])->name('products.destroy');
    Route::post('/produits/{product}/stock', [ShopAdmin\ProductController::class, 'adjustStock'])->name('products.stock');

    Route::get('/categories', [ShopAdmin\CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [ShopAdmin\CategoryController::class, 'store'])->name('categories.store');
    Route::patch('/categories/{category}', [ShopAdmin\CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [ShopAdmin\CategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('/codes-promo', [ShopAdmin\CouponController::class, 'index'])->name('coupons.index');
    Route::get('/codes-promo/nouveau', [ShopAdmin\CouponController::class, 'create'])->name('coupons.create');
    Route::post('/codes-promo', [ShopAdmin\CouponController::class, 'store'])->name('coupons.store');
    Route::get('/codes-promo/{coupon}', [ShopAdmin\CouponController::class, 'edit'])->name('coupons.edit');
    Route::put('/codes-promo/{coupon}', [ShopAdmin\CouponController::class, 'update'])->name('coupons.update');
    Route::delete('/codes-promo/{coupon}', [ShopAdmin\CouponController::class, 'destroy'])->name('coupons.destroy');

    Route::get('/avis', [ShopAdmin\ReviewController::class, 'index'])->name('reviews.index');
    Route::patch('/avis/{review}', [ShopAdmin\ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/avis/{review}', [ShopAdmin\ReviewController::class, 'destroy'])->name('reviews.destroy');

    Route::get('/import-export', [ShopAdmin\ImportExportController::class, 'index'])->name('import-export');
    Route::get('/export/catalogue', [ShopAdmin\ImportExportController::class, 'exportCatalog'])->name('export.catalog');
    Route::get('/export/ventes', [ShopAdmin\ImportExportController::class, 'exportSales'])->name('export.sales');
    Route::post('/import/catalogue', [ShopAdmin\ImportExportController::class, 'import'])->middleware('throttle:10,1')->name('import.catalog');
});

Route::bind('product', fn (string $value) => request()->routeIs('shop-admin.*')
    ? Product::withTrashed()->where('slug', $value)->firstOrFail()
    : Product::query()->where('slug', $value)->firstOrFail());

Route::get('/sitemap.xml', function () {
    $urls = collect(['home', 'about', 'services', 'case-studies', 'formation', 'shop.index', 'contact', 'mentions-legales', 'politique-confidentialite', 'cgv'])
        ->map(fn ($name) => route($name))
        ->merge(Product::query()->published()->orderBy('name')->pluck('slug')->map(fn ($slug) => route('shop.product', $slug)));

    return response()->view('sitemap', ['urls' => $urls])->header('Content-Type', 'application/xml');
})->name('sitemap');

require __DIR__.'/auth.php';
