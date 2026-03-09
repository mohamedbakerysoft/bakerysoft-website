<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ConversionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/sitemap.xml', [PageController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [PageController::class, 'robots'])->name('robots');
Route::get('/ads.txt', [PageController::class, 'ads'])->name('ads');
Route::get('/من-نحن', [PageController::class, 'about'])->name('about');
Route::get('/سياسة-الخصوصية', [PageController::class, 'privacy'])->name('privacy');
Route::get('/اتصل-بنا', [PageController::class, 'contact'])->name('contact');
Route::get('/المحولات/{from}/{to}', ConversionController::class)->name('conversion.show');
Route::get('/{categorySlug}/{toolSlug}', ToolController::class)->name('tool.show');
Route::get('/{categorySlug}', CategoryController::class)->name('category.show');
