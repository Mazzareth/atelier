<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\ArtistProfileController;
use App\Http\Controllers\Atelier\AtelierDashboardController;
use App\Http\Controllers\Atelier\PageBuilderController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\RoleSwitchController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\ConversationNotificationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\ProfileConfigController;
use App\Http\Controllers\Commission\ArtistRequestInboxController;
use App\Http\Controllers\Commission\CommissionMessageController;
use App\Http\Controllers\Commission\CommissionNotificationController;
use App\Http\Controllers\Commission\CommissionRequestController;
use App\Http\Controllers\Commission\WorkspaceController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [WelcomeController::class, 'index']);

// Pricing / Plans
Route::get('/pricing', function() {
    return view('pricing');
})->name('pricing');

Route::get('/plans', function() {
    return view('plans');
})->middleware('auth')->name('plans');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated User Routes (Base/Commissioner View)
Route::middleware(['auth'])->group(function () {
    // Browse Artists
    Route::get('/browse', [ArtistProfileController::class, 'browse'])->name('browse');

    // The main personal feed (consumer view)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/my-requests', [DashboardController::class, 'myRequests'])->name('commission.index');

    Route::get('/messages', [ConversationController::class, 'index'])->name('conversations.index');
    Route::get('/messages/start/{username}', [ConversationController::class, 'start'])->name('conversations.start');
    Route::get('/messages/{conversation}', [ConversationController::class, 'show'])->name('conversations.show');
    Route::get('/messages/{conversation}/attachments/{message}/{index}', [ConversationController::class, 'attachment'])->whereNumber('index')->name('conversations.messages.attachment');
    Route::post('/messages/{conversation}/send', [ConversationController::class, 'storeMessage'])->name('conversations.messages.store');
    Route::delete('/messages/{conversation}', [ConversationController::class, 'destroy'])->name('conversations.destroy');
    Route::get('/messages/notifications/summary', [ConversationNotificationController::class, 'summary'])->name('conversations.notifications.summary');

    // Profile Switcher for Artists/Admins
    Route::post('/switch-profile/{mode}', [RoleSwitchController::class, 'switchProfile'])
        ->name('profile.switch');

    Route::post('/follow/{username}', [FollowController::class, 'toggle'])->name('follow.toggle');

    Route::get('/commission/request/{username}', [CommissionRequestController::class, 'create'])->name('commission.create');
    Route::post('/commission/request/{username}', [CommissionRequestController::class, 'store'])->name('commission.store');
    Route::get('/commission/requests/{commissionRequest}', [CommissionRequestController::class, 'show'])->name('commission.show');
    Route::get('/commission/requests/{commissionRequest}/messages/{message}/attachments/{index}', [CommissionMessageController::class, 'attachment'])->whereNumber('index')->name('commission.messages.attachment');
    Route::post('/commission/requests/{commissionRequest}/messages', [CommissionMessageController::class, 'store'])->name('commission.messages.store');
    Route::get('/commission/notifications/summary', [CommissionNotificationController::class, 'summary'])->name('commission.notifications.summary');
    Route::get('/commission/requests/{commissionRequest}/thread', [CommissionNotificationController::class, 'thread'])->name('commission.thread');
});

// Artist Workspace (Atelier)
Route::middleware(['auth', 'role:artist'])->prefix('atelier')->name('artist.')->group(function () {
    Route::get('/dashboard', [AtelierDashboardController::class, 'index'])->name('dashboard');

    Route::get('/requests', [ArtistRequestInboxController::class, 'index'])->name('requests.index');
    Route::get('/commissions', [ArtistRequestInboxController::class, 'tracker'])->name('artist.commissions.index');
    Route::get('/workspace/{commissionRequest?}', [WorkspaceController::class, 'index'])->name('workspace.show');
    Route::post('/workspace/manual-commission', [WorkspaceController::class, 'storeManualCommission'])->name('workspace.manual.store');
    Route::post('/workspace/{commissionRequest}/stage', [WorkspaceController::class, 'updateStage'])->name('workspace.stage');
    Route::post('/workspace/{commissionRequest}/items/upload', [WorkspaceController::class, 'uploadAsset'])->name('workspace.items.upload');
    Route::post('/workspace/{commissionRequest}/items/note', [WorkspaceController::class, 'storeNote'])->name('workspace.items.note');
    Route::post('/workspace/{commissionRequest}/items/group', [WorkspaceController::class, 'storeGroup'])->name('workspace.items.group');
    Route::get('/workspace/{commissionRequest}/items/{workspaceItem}/asset', [WorkspaceController::class, 'asset'])->name('workspace.items.asset');
    Route::patch('/workspace/{commissionRequest}/items/{workspaceItem}', [WorkspaceController::class, 'updateItem'])->name('workspace.items.update');
    Route::delete('/workspace/{commissionRequest}/items/{workspaceItem}', [WorkspaceController::class, 'deleteItem'])->name('workspace.items.delete');
    Route::post('/workspace/{commissionRequest}/connections', [WorkspaceController::class, 'storeConnection'])->name('workspace.connections.store');
    Route::delete('/workspace/{commissionRequest}/connections/{workspaceConnection}', [WorkspaceController::class, 'deleteConnection'])->name('workspace.connections.delete');
    Route::post('/requests/{commissionRequest}/respond', [ArtistRequestInboxController::class, 'respond'])->name('requests.respond');
    Route::post('/requests/{commissionRequest}/undo', [ArtistRequestInboxController::class, 'undo'])->name('requests.undo');
    Route::post('/requests/{commissionRequest}/tracker-stage', [ArtistRequestInboxController::class, 'updateTrackerStage'])->name('requests.tracker-stage');
});

// Profile Builder Routes
Route::post('/profile/save-layout', [ArtistProfileController::class, 'saveLayout'])
    ->middleware(['auth', 'role:artist'])
    ->name('artist.profile.save');
Route::post('/profile/configs', [ProfileConfigController::class, 'saveConfig'])
    ->middleware(['auth', 'role:artist'])
    ->name('artist.profile.configs.save');
Route::post('/profile/configs/load', [ProfileConfigController::class, 'loadConfig'])
    ->middleware(['auth', 'role:artist'])
    ->name('artist.profile.configs.load');
Route::delete('/profile/configs/{id}', [ProfileConfigController::class, 'deleteConfig'])
    ->middleware(['auth', 'role:artist'])
    ->name('artist.profile.configs.delete');
Route::delete('/profile/module/{id}', [ArtistProfileController::class, 'deleteModule'])
    ->middleware(['auth', 'role:artist']);
Route::patch('/profile/module/{id}/settings', [ArtistProfileController::class, 'updateModuleSettings'])
    ->middleware(['auth', 'role:artist']);
Route::post('/profile/module/{id}/gallery-images', [GalleryController::class, 'uploadGalleryImages'])
    ->middleware(['auth', 'role:artist'])
    ->name('artist.profile.gallery.upload');
Route::get('/profile/module/{id}/gallery-images/{imageIndex}', [GalleryController::class, 'showGalleryImage'])
    ->whereNumber('imageIndex')
    ->name('artist.profile.gallery.show');
Route::delete('/profile/module/{id}/gallery-images/{imageIndex}', [GalleryController::class, 'deleteGalleryImage'])
    ->middleware(['auth', 'role:artist'])
    ->whereNumber('imageIndex')
    ->name('artist.profile.gallery.delete');

// Platform Admin Area
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
});

// Public Artist Profile (Must be at the bottom to avoid catching other routes)
Route::get('/{username}', [ArtistProfileController::class, 'show'])->name('artist.profile');
