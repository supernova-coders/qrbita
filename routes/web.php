<?php

use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AiVCardController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PublicQrRedirectController;
use App\Http\Controllers\PublicVCardController;
use App\Http\Controllers\PublishedVCardController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\VCardContactController;
use App\Http\Controllers\VCardController;
use App\Http\Controllers\VCardLinkRedirectController;
use App\Http\Controllers\VCardMetricsController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');

Route::get('v/{vCard:slug}', PublicVCardController::class)
    ->name('public.vcards.show');
Route::get('v/{vCard:slug}/contact.vcf', VCardContactController::class)
    ->name('public.vcards.contact');
Route::get('v/{vCard:slug}/links/{link}', VCardLinkRedirectController::class)
    ->scopeBindings()
    ->name('public.vcards.links.redirect');
Route::get('q/{qrCode:code}', PublicQrRedirectController::class)
    ->name('public.qr.redirect');

Route::middleware(['auth', 'verified', 'active'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::get('vcards/create', [VCardController::class, 'create'])->name('vcards.create');
    Route::post('vcards', [VCardController::class, 'store'])->name('vcards.store');
    Route::get('vcards/{vCard:slug}', [VCardController::class, 'show'])->name('vcards.show');
    Route::get('vcards/{vCard:slug}/edit', [VCardController::class, 'edit'])->name('vcards.edit');
    Route::put('vcards/{vCard:slug}', [VCardController::class, 'update'])->name('vcards.update');

    Route::post('vcards/{vCard:slug}/publication', [PublishedVCardController::class, 'store'])
        ->name('vcards.publication.store');
    Route::delete('vcards/{vCard:slug}/publication', [PublishedVCardController::class, 'destroy'])
        ->name('vcards.publication.destroy');
    Route::get('vcards/{vCard:slug}/qr.png', QrCodeController::class)
        ->name('vcards.qr');
    Route::get('vcards/{vCard:slug}/metrics', VCardMetricsController::class)
        ->name('vcards.metrics');

    Route::post('vcard-ai/proposals', AiVCardController::class)
        ->middleware('throttle:6,1')
        ->name('vcard-ai.proposals.store');

    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
        Route::patch('users/{user}/status', [AdminUserController::class, 'update'])
            ->name('users.status.update');
    });
});

require __DIR__.'/settings.php';
