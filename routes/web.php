<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TmdbProxyController;
use App\Http\Controllers\DownloadLinkController;

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

Route::get('/api/config/categories', [\App\Http\Controllers\ConfigController::class, 'categories']);
Route::get('/api/config/servers',    [\App\Http\Controllers\ConfigController::class, 'servers']);
Route::get('/api/config/home-sections', [\App\Http\Controllers\ConfigController::class, 'homeSections']);

Route::get('/api/tmdb/{path}', [TmdbProxyController::class, 'proxy'])->where('path', '.*');

// Home Sections Admin Management
Route::get('/admin/home-section-manager', [\App\Http\Controllers\HomeSectionController::class, 'managerView'])->name('admin.home-section-manager');
Route::prefix('admin/api/home-sections')->group(function () {
    Route::get('/', [App\Http\Controllers\HomeSectionController::class, 'index']);
    Route::post('/', [App\Http\Controllers\HomeSectionController::class, 'store']);
    Route::put('/{id}', [App\Http\Controllers\HomeSectionController::class, 'update']);
    Route::delete('/{id}', [App\Http\Controllers\HomeSectionController::class, 'destroy']);
});

// Custom Movies Public API
Route::get('/api/custom-content', [\App\Http\Controllers\CustomMovieController::class, 'publicIndex']);
Route::get('/api/search/custom', [\App\Http\Controllers\CustomMovieController::class, 'search']);
Route::get('/api/custom-movie/{id}', [\App\Http\Controllers\CustomMovieController::class, 'getDetails']);

Route::get('/details/custom/{id}', function ($id) {
    return view('details', ['type' => 'custom', 'id' => $id]);
})->name('details.custom');

Route::get('/play/custom/{id}', function ($id) {
    return view('player', [
        'type'    => 'custom',
        'id'      => $id,
        'season'  => request()->query('season'),
        'episode' => request()->query('episode')
    ]);
})->name('play.custom');

// Custom Movies Admin Management
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

// Download Links Public API
Route::get('/api/download-links/{type}/{id}', [DownloadLinkController::class, 'index']);

// Download Manager Admin Management
Route::get('/admin/download-manager', [DownloadLinkController::class, 'managerView'])->name('admin.download-manager');
Route::prefix('admin/api/download-links')->group(function () {
    Route::get('/',              [DownloadLinkController::class, 'adminIndex']);
    Route::post('/',             [DownloadLinkController::class, 'store']);
    Route::put('/{id}',          [DownloadLinkController::class, 'update']);
    Route::delete('/{id}',       [DownloadLinkController::class, 'destroy']);
});

