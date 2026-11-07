<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\AdminActivityLogController;
use App\Http\Controllers\Admin\CertificateController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InstructorController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\TwoFactorSecurityController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VerificationLogController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\TwoFactorChallengeController;
use App\Http\Controllers\CertificateDownloadController;
use App\Http\Controllers\CertificatePreviewController;
use App\Http\Controllers\VerifyController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/verify');

Route::get('/verify', [VerifyController::class, 'form'])->name('verify.form');
Route::post('/verify', [VerifyController::class, 'lookup'])
    ->middleware('throttle:10,1')
    ->name('verify.lookup');

Route::get('/verify/{code}', [VerifyController::class, 'show'])
    ->middleware('throttle:verify')
    ->where('code', '[0-9A-HJKMNP-TV-Z]{26}')
    ->name('verify.show');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');

    Route::get('/forgot-password', [ForgotPasswordController::class, 'create'])
        ->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('password.email');

    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'create'])
        ->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('password.update');

    Route::get('/two-factor-challenge', [TwoFactorChallengeController::class, 'create'])
        ->middleware('throttle:10,1')
        ->name('two-factor.login');
    Route::post('/two-factor-challenge', [TwoFactorChallengeController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('two-factor.verify');
});

Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function (): void {
    Route::get('/certificates/{certificate}/pdf', CertificateDownloadController::class)
        ->name('certificates.pdf');
    Route::get('/certificates/{certificate}/preview', CertificatePreviewController::class)
        ->name('certificates.preview');
});

Route::middleware('auth')
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::get('/', DashboardController::class)->name('dashboard');

        Route::resource('courses', CourseController::class);
        Route::resource('instructors', InstructorController::class)
            ->except(['show']);

        Route::post('certificates/suggest', [CertificateController::class, 'suggest'])
            ->name('certificates.suggest');
        Route::get('certificates/import', [CertificateController::class, 'importForm'])
            ->name('certificates.import.form');
        Route::post('certificates/import', [CertificateController::class, 'import'])
            ->name('certificates.import');
        Route::get('certificates/import/template', [CertificateController::class, 'importTemplate'])
            ->name('certificates.import.template');
        Route::post('certificates/{certificate}/issue', [CertificateController::class, 'issue'])
            ->name('certificates.issue');
        Route::post('certificates/{certificate}/revoke', [CertificateController::class, 'revoke'])
            ->name('certificates.revoke');
        Route::resource('certificates', CertificateController::class);

        Route::get('verification-logs', [VerificationLogController::class, 'index'])
            ->name('logs.index');

        Route::get('activity-logs', [AdminActivityLogController::class, 'index'])
            ->name('activity-logs.index');
        Route::get('activity-logs/{log}', [AdminActivityLogController::class, 'show'])
            ->name('activity-logs.show');

        Route::get('security', [TwoFactorSecurityController::class, 'show'])
            ->name('security.show');
        Route::post('security/enable', [TwoFactorSecurityController::class, 'enable'])
            ->name('security.enable');
        Route::post('security/confirm', [TwoFactorSecurityController::class, 'confirm'])
            ->name('security.confirm');
        Route::post('security/disable', [TwoFactorSecurityController::class, 'disable'])
            ->name('security.disable');

        Route::get('settings', [SettingsController::class, 'edit'])
            ->name('settings.edit');
        Route::put('settings', [SettingsController::class, 'update'])
            ->name('settings.update');

        Route::resource('users', UserController::class)->except(['show']);
    });
