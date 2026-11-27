<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Public;
use Illuminate\Support\Facades\Route;

Route::get('/', Public\HomeController::class)->name('home');
Route::get('program', [Public\ProgramController::class, 'index'])->name('programs.index');
Route::get('program/{program:slug}', [Public\ProgramController::class, 'show'])->name('programs.show');
Route::get('artikel', [Public\PostController::class, 'index'])->name('posts.index');
Route::get('artikel/{post:slug}', [Public\PostController::class, 'show'])->name('posts.show');
Route::get('tentang', [Public\PageController::class, 'about'])->name('about');
Route::get('faq', [Public\FaqController::class, 'index'])->name('faq');
Route::get('galeri', [Public\GalleryController::class, 'index'])->name('gallery');

Route::get('daftar', [Public\ApplicationController::class, 'create'])->name('applications.create');
Route::post('daftar', [Public\ApplicationController::class, 'store'])->middleware('throttle:applications')->name('applications.store');
Route::get('daftar/terkirim', [Public\ApplicationController::class, 'submitted'])->name('applications.submitted');
Route::get('cek-status', [Public\ApplicationStatusController::class, 'show'])->name('applications.status');
Route::post('cek-status', [Public\ApplicationStatusController::class, 'check'])->middleware('throttle:status-check')->name('applications.status.check');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [LoginController::class, 'create'])->name('login');
        Route::post('login', [LoginController::class, 'store'])->name('login.store');
    });

    Route::middleware('auth')->group(function () {
        Route::get('/', Admin\DashboardController::class)->name('dashboard');
        Route::post('logout', [LoginController::class, 'destroy'])->name('logout');

        // Public content: admins and editors.
        Route::resource('posts', Admin\PostController::class)->except('show');
        Route::resource('faqs', Admin\FaqController::class)->except('show');
        Route::resource('gallery', Admin\GalleryItemController::class)->except('show');
        Route::get('pages', [Admin\PageController::class, 'index'])->name('pages.index');
        Route::get('pages/{key}/edit', [Admin\PageController::class, 'edit'])->name('pages.edit');
        Route::put('pages/{key}', [Admin\PageController::class, 'update'])->name('pages.update');

        // Recruitment and site settings: admins only.
        Route::middleware('can:manage-recruitment')->group(function () {
            Route::resource('programs', Admin\ProgramController::class)->except('show');
            Route::get('settings', [Admin\SettingController::class, 'edit'])->name('settings.edit');
            Route::put('settings', [Admin\SettingController::class, 'update'])->name('settings.update');
        });
    });
});
