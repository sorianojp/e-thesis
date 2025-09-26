<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ThesisController;
use App\Http\Controllers\Admin\ThesisReviewController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Models\Thesis;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VerifyController;


Route::get('/verify/{token}', [VerifyController::class, 'show'])->name('verify.show');
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/', fn () => view('dashboard'))->name('dashboard');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | Student-facing Thesis routes
    |--------------------------------------------------------------------------
    | Uses implicit model binding {thesis} -> App\Models\Thesis
    */
    Route::get('/theses', [ThesisController::class, 'index'])->name('theses.index');
    Route::get('/theses/create', [ThesisController::class, 'create'])->name('theses.create');
    Route::post('/theses', [ThesisController::class, 'store'])->name('theses.store');


    // Secure file access (owner OR admin)
    Route::get('/theses/{thesis}/download/{type}', [ThesisController::class, 'download'])
        ->whereIn('type', ['thesis', 'endorsement', 'abstract'])
        ->middleware('can:view,thesis')
        ->name('theses.download');

    /*
    |--------------------------------------------------------------------------
    | Certificate (owner OR admin) – only when approved
    |--------------------------------------------------------------------------
    */
    Route::get('/theses/{thesis}/certificate', [ThesisReviewController::class, 'certificate'])
        ->middleware('can:downloadCertificate,thesis')
        ->name('theses.certificate');

    Route::prefix('adviser')
        ->middleware(['role:adviser'])
        ->group(function () {
            Route::get('/theses', [ThesisReviewController::class, 'index'])
                ->name('adviser.theses.index');

            Route::get('/theses/{thesis}', [ThesisReviewController::class, 'show'])
                ->middleware('can:view,thesis')
                ->name('adviser.theses.show');

            Route::post('/theses/{thesis}/approve', [ThesisReviewController::class, 'approve'])
                ->middleware('can:review,thesis')
                ->name('adviser.theses.approve');

            Route::get('/theses/{thesis}/panel', [ThesisReviewController::class, 'editPanel'])
                ->middleware('can:review,thesis')
                ->name('adviser.theses.panel.edit');

            Route::post('/theses/{thesis}/panel', [ThesisReviewController::class, 'updatePanel'])
                ->middleware('can:review,thesis')
                ->name('adviser.theses.panel.update');

            Route::post('/theses/{thesis}/reject', [ThesisReviewController::class, 'reject'])
                ->middleware('can:review,thesis')
                ->name('adviser.theses.reject');
        });

    Route::prefix('admin')
        ->middleware(['role:admin'])
        ->group(function () {
            Route::get('/theses', [ThesisReviewController::class, 'index'])
                ->name('admin.theses.index');

            Route::get('/theses/{thesis}', [ThesisReviewController::class, 'show'])
                ->middleware('can:view,thesis')
                ->name('admin.theses.show');

            Route::post('/theses/{thesis}/approve', [ThesisReviewController::class, 'approve'])
                ->middleware('can:review,thesis')
                ->name('admin.theses.approve');

            Route::get('/theses/{thesis}/panel', [ThesisReviewController::class, 'editPanel'])
                ->middleware('can:review,thesis')
                ->name('admin.theses.panel.edit');

            Route::post('/theses/{thesis}/panel', [ThesisReviewController::class, 'updatePanel'])
                ->middleware('can:review,thesis')
                ->name('admin.theses.panel.update');

            Route::post('/theses/{thesis}/reject', [ThesisReviewController::class, 'reject'])
                ->middleware('can:review,thesis')
                ->name('admin.theses.reject');

            Route::get('/users', [UserManagementController::class, 'index'])
                ->name('admin.users.index');

            Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])
                ->name('admin.users.edit');

            Route::put('/users/{user}', [UserManagementController::class, 'update'])
                ->name('admin.users.update');
        });
});

require __DIR__ . '/auth.php';
