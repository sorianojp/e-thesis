<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ThesisController;
use App\Http\Controllers\Admin\ThesisReviewController;
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
        ->whereIn('type', ['thesis', 'endorsement'])
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

    /*
    |--------------------------------------------------------------------------
    | Admin review area
    |--------------------------------------------------------------------------
    | Your ThesisPolicy::admin(User $user) handles this via 'can:admin,<ModelClass>'
    */
    Route::middleware('can:admin,' . Thesis::class)->group(function () {
        Route::get('/admin/theses', [ThesisReviewController::class, 'index'])
            ->name('admin.theses.index');

        Route::get('/admin/theses/{thesis}', [ThesisReviewController::class, 'show'])
            ->name('admin.theses.show');

        Route::post('/admin/theses/{thesis}/approve', [ThesisReviewController::class, 'approve'])
            ->name('admin.theses.approve');

        Route::post('/admin/theses/{thesis}/reject', [ThesisReviewController::class, 'reject'])
            ->name('admin.theses.reject');
    });
});

require __DIR__ . '/auth.php';
