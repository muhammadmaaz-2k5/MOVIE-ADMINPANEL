<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TmdbProxyController;
use App\Http\Controllers\DownloadLinkController;
use App\Http\Controllers\AdminAuthController;
use App\Models\Setting;

/*
|--------------------------------------------------------------------------
| Public Web Frontend Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/movies', function () {
    return view('movies');
})->name('movies');

Route::get('/tv-shows', function () {
    return view('tv-shows');
})->name('tv-shows');

Route::get('/anime', function () {
    return view('anime');
})->name('anime');

Route::get('/search', function () {
    return view('search');
})->name('search');

Route::get('/details/{type}/{id}', function ($type, $id) {
    return view('details', ['type' => $type, 'id' => $id]);
})->name('details');

Route::get('/actor/{id}', function ($id) {
    return view('actor', ['id' => $id]);
})->name('actor');

Route::get('/play/{type}/{id}', function ($type, $id) {
    if (Setting::isSafeReviewMode()) {
        return redirect()->route('details', ['type' => $type, 'id' => $id]);
    }

    return view('player', [
        'type'    => $type,
        'id'      => $id,
        'season'  => request()->query('season'),
        'episode' => request()->query('episode')
    ]);
})->name('play');

Route::get('/languages', function () {
    return view('languages');
})->name('languages');

Route::get('/details/custom/{id}', function ($id) {
    return view('details', ['type' => 'custom', 'id' => $id]);
})->name('details.custom');

Route::get('/play/custom/{id}', function ($id) {
    if (Setting::isSafeReviewMode()) {
        return redirect()->route('details.custom', ['id' => $id]);
    }

    return view('player', [
        'type'    => 'custom',
        'id'      => $id,
        'season'  => request()->query('season'),
        'episode' => request()->query('episode')
    ]);
})->name('play.custom');

/*
|--------------------------------------------------------------------------
| Public Mobile App & Web APIs (Unrestricted for Mobile Clients)
|--------------------------------------------------------------------------
*/
Route::get('/api/config/categories', [\App\Http\Controllers\ConfigController::class, 'categories']);
Route::get('/api/config/servers',    [\App\Http\Controllers\ConfigController::class, 'servers']);
Route::get('/api/config/home-sections', [\App\Http\Controllers\ConfigController::class, 'homeSections']);
Route::get('/api/config/settings',    [\App\Http\Controllers\ConfigController::class, 'globalSettings']);
Route::get('/api/home/feed',          [\App\Http\Controllers\HomeFeedController::class, 'feed']);
Route::get('/api/midnight/feed',      [\App\Http\Controllers\MidnightFeedController::class, 'feed']);

// TMDB Proxy API
Route::get('/api/tmdb/{any}', [\App\Http\Controllers\TmdbProxyController::class, 'proxy'])->where('any', '.*');

// Custom Movies Public API
Route::get('/api/custom-content', [\App\Http\Controllers\CustomMovieController::class, 'publicIndex']);
Route::get('/api/search/custom', [\App\Http\Controllers\CustomMovieController::class, 'search']);
Route::get('/api/custom-movie/{id}', [\App\Http\Controllers\CustomMovieController::class, 'getDetails']);

// Download Links Public API
Route::get('/api/download-links/{type}/{id}', [DownloadLinkController::class, 'index']);

// Promoted More Apps Public API
Route::get('/api/more-apps', [\App\Http\Controllers\PromotedAppController::class, 'publicIndex']);

/*
|--------------------------------------------------------------------------
| Admin Authentication Routes (Public Login & Secure Logout)
|--------------------------------------------------------------------------
*/
Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

/*
|--------------------------------------------------------------------------
| Protected Admin Dashboard & Management APIs
|--------------------------------------------------------------------------
*/
Route::middleware(['web', 'admin.auth'])->group(function () {

    // Credential & Account Management
    Route::post('/admin/api/settings/update-credentials', [AdminAuthController::class, 'updateCredentials'])->name('admin.update-credentials');

    // Home Sections Manager
    Route::get('/admin/home-section-manager', [\App\Http\Controllers\HomeSectionController::class, 'managerView'])->name('admin.home-section-manager');
    Route::prefix('admin/api/home-sections')->group(function () {
        Route::get('/', [\App\Http\Controllers\HomeSectionController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\HomeSectionController::class, 'store']);
        Route::put('/{id}', [\App\Http\Controllers\HomeSectionController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\HomeSectionController::class, 'destroy']);
    });

    // Midnight 18+ Manager
    Route::get('/admin/midnight-manager', [\App\Http\Controllers\MidnightSectionController::class, 'managerView'])->name('admin.midnight-manager');
    Route::prefix('admin/api/midnight-sections')->group(function () {
        Route::get('/', [\App\Http\Controllers\MidnightSectionController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\MidnightSectionController::class, 'store']);
        Route::put('/{id}', [\App\Http\Controllers\MidnightSectionController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\MidnightSectionController::class, 'destroy']);
    });
    Route::prefix('admin/api/midnight-content')->group(function () {
        Route::get('/', [\App\Http\Controllers\MidnightSectionController::class, 'content']);
        Route::post('/toggle/{id}', [\App\Http\Controllers\MidnightSectionController::class, 'toggleContent']);
        Route::get('/search-available', [\App\Http\Controllers\MidnightSectionController::class, 'searchAvailable']);
    });

    // Global Settings
    Route::get('/admin/settings', function () { return view('admin.settings'); })->name('admin.settings');
    Route::prefix('admin/api/settings')->group(function () {
        Route::get('/', [\App\Http\Controllers\SettingsController::class, 'index']);
        Route::put('/', [\App\Http\Controllers\SettingsController::class, 'update']);
        Route::post('/enable-all-ads', [\App\Http\Controllers\SettingsController::class, 'enableAllAds']);
        Route::post('/disable-all-ads', [\App\Http\Controllers\SettingsController::class, 'disableAllAds']);
        Route::post('/set-safe-review', [\App\Http\Controllers\SettingsController::class, 'setSafeReviewMode']);
        Route::post('/set-live-mode', [\App\Http\Controllers\SettingsController::class, 'setLiveMode']);
    });

    // Notification Manager
    Route::get('/admin/notification-manager', [\App\Http\Controllers\NotificationController::class, 'managerView'])->name('admin.notification-manager');
    Route::post('/admin/api/notifications/send', [\App\Http\Controllers\NotificationController::class, 'send']);
    Route::prefix('admin/api/scheduled-notifications')->group(function () {
        Route::get('/', [\App\Http\Controllers\NotificationController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\NotificationController::class, 'store']);
        Route::post('/send-random', [\App\Http\Controllers\NotificationController::class, 'sendRandom']);
        Route::post('/{id}', [\App\Http\Controllers\NotificationController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\NotificationController::class, 'destroy']);
        Route::post('/{id}/send', [\App\Http\Controllers\NotificationController::class, 'sendSpecific']);
    });

    // Video Server Admin Management
    Route::get('/admin/video-servers', function () { return view('admin.video-servers'); })->name('admin.video-servers');
    Route::prefix('admin/api/video-servers')->group(function () {
        Route::get('/', [\App\Http\Controllers\VideoServerController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\VideoServerController::class, 'store']);
        Route::put('/{id}', [\App\Http\Controllers\VideoServerController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\VideoServerController::class, 'destroy']);
    });

    // Custom Movies & Series Admin Management
    Route::get('/admin/movie-manager', [\App\Http\Controllers\CustomMovieController::class, 'managerView'])->name('admin.movie-manager');
    Route::get('/admin/tv-manager', [\App\Http\Controllers\CustomMovieController::class, 'tvManagerView'])->name('admin.tv-manager');
    Route::get('/admin/anime-manager', [\App\Http\Controllers\CustomMovieController::class, 'animeManagerView'])->name('admin.anime-manager');
    Route::prefix('admin/api/custom-movies')->group(function () {
        Route::get('/',              [\App\Http\Controllers\CustomMovieController::class, 'adminIndex']);
        Route::post('/',             [\App\Http\Controllers\CustomMovieController::class, 'store']);
        Route::put('/{id}',          [\App\Http\Controllers\CustomMovieController::class, 'update']);
        Route::delete('/{id}',       [\App\Http\Controllers\CustomMovieController::class, 'destroy']);
        Route::get('/{id}/streams',  [\App\Http\Controllers\CustomMovieController::class, 'getStreams']);
        Route::post('/{id}/streams', [\App\Http\Controllers\CustomMovieController::class, 'storeStream']);
    });
    Route::prefix('admin/api/custom-streams')->group(function () {
        Route::put('/{id}',    [\App\Http\Controllers\CustomMovieController::class, 'updateStream']);
        Route::delete('/{id}', [\App\Http\Controllers\CustomMovieController::class, 'destroyStream']);
    });

    // Download Manager Admin Management
    Route::get('/admin/download-manager', [DownloadLinkController::class, 'managerView'])->name('admin.download-manager');
    Route::prefix('admin/api/download-links')->group(function () {
        Route::get('/',              [DownloadLinkController::class, 'adminIndex']);
        Route::post('/',             [DownloadLinkController::class, 'store']);
        Route::put('/{id}',          [DownloadLinkController::class, 'update']);
        Route::delete('/{id}',       [DownloadLinkController::class, 'destroy']);
    });

    // Promoted Apps Admin Management
    Route::get('/admin/promoted-apps', [\App\Http\Controllers\PromotedAppController::class, 'managerView'])->name('admin.promoted-apps');
    Route::prefix('admin/api/promoted-apps')->group(function () {
        Route::get('/',                    [\App\Http\Controllers\PromotedAppController::class, 'adminIndex']);
        Route::post('/',                   [\App\Http\Controllers\PromotedAppController::class, 'store']);
        Route::put('/{id}',                [\App\Http\Controllers\PromotedAppController::class, 'update']);
        Route::delete('/{id}',             [\App\Http\Controllers\PromotedAppController::class, 'destroy']);
        Route::post('/{id}/toggle-status',   [\App\Http\Controllers\PromotedAppController::class, 'toggleStatus']);
        Route::post('/{id}/toggle-featured', [\App\Http\Controllers\PromotedAppController::class, 'toggleFeatured']);
    });
});
