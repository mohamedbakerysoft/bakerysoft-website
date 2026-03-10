<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ConversionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ToolController;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Route;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

Route::get('/', HomeController::class)->name('home');
Route::get('/sitemap.xml', [PageController::class, 'sitemap'])
    ->withoutMiddleware([EncryptCookies::class, AddQueuedCookiesToResponse::class, StartSession::class, ShareErrorsFromSession::class, ValidateCsrfToken::class])
    ->name('sitemap');
Route::get('/robots.txt', [PageController::class, 'robots'])
    ->withoutMiddleware([EncryptCookies::class, AddQueuedCookiesToResponse::class, StartSession::class, ShareErrorsFromSession::class, ValidateCsrfToken::class])
    ->name('robots');
Route::get('/ads.txt', [PageController::class, 'ads'])
    ->withoutMiddleware([EncryptCookies::class, AddQueuedCookiesToResponse::class, StartSession::class, ShareErrorsFromSession::class, ValidateCsrfToken::class])
    ->name('ads');
Route::get('/من-نحن', [PageController::class, 'about'])->name('about');
Route::get('/سياسة-الخصوصية', [PageController::class, 'privacy'])->name('privacy');
Route::get('/اتصل-بنا', [PageController::class, 'contact'])->name('contact');
Route::get('/المحولات/{from}/{to}', ConversionController::class)->name('conversion.show');
Route::get('/{categorySlug}/{toolSlug}', ToolController::class)->name('tool.show');
Route::get('/{categorySlug}', CategoryController::class)->name('category.show');
