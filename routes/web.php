<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ThesisController;
use App\Http\Controllers\ThesisTitleController;
use App\Http\Controllers\Admin\ThesisReviewController;
use App\Http\Controllers\Admin\PostgradThesisController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\VerifyController;
use App\Models\PostgradThesis;
use App\Models\Thesis;
use App\Models\ThesisTitle;
use App\Models\User;
use App\Http\Controllers\Adviser\ThesisTitleReviewController;
use Illuminate\Support\Facades\Route;

Route::get('/verify/{token}', [VerifyController::class, 'show'])->name('verify.show');
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/', function () {
        $user = auth()->user();

        $thesisStats = null;
        $adviserStats = null;
        $adminStats = null;

        if ($user->isStudent()) {
            $statusCounts = Thesis::query()
                ->selectRaw('status, COUNT(*) as count')
                ->whereHas('thesisTitle', fn ($q) => $q->where('user_id', $user->id))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->map(fn ($count) => (int) $count);

            $leaderTitleCount = ThesisTitle::query()
                ->where('user_id', $user->id)
                ->count();

            $teamTitlesCount = ThesisTitle::query()
                ->whereHas('members', fn ($q) => $q->where('users.id', $user->id))
                ->count();

            $thesisStats = [
                'uploaded' => $statusCounts->sum(),
                'pending' => $statusCounts['pending'] ?? 0,
                'approved' => $statusCounts['approved'] ?? 0,
                'rejected' => $statusCounts['rejected'] ?? 0,
                'leader_titles' => $leaderTitleCount,
                'team_titles' => $teamTitlesCount,
            ];
        } elseif ($user->isAdviser()) {
            $adviserTheses = Thesis::query()
                ->whereHas('thesisTitle', fn ($q) => $q->where('adviser_id', $user->id));

            $statusCounts = (clone $adviserTheses)
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->map(fn ($count) => (int) $count);

            $adviserStats = [
                'theses' => (clone $adviserTheses)->count(),
                'students' => ThesisTitle::query()
                    ->where('adviser_id', $user->id)
                    ->distinct('user_id')
                    ->count('user_id'),
                'thesis_titles' => ThesisTitle::query()
                    ->where('adviser_id', $user->id)
                    ->count(),
                'pending' => $statusCounts['pending'] ?? 0,
                'approved' => $statusCounts['approved'] ?? 0,
                'rejected' => $statusCounts['rejected'] ?? 0,
            ];
        } elseif ($user->isAdmin()) {
            $statusCounts = Thesis::query()
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->map(fn ($count) => (int) $count);

            $roleCounts = User::query()
                ->selectRaw('role, COUNT(*) as count')
                ->whereIn('role', [User::ROLE_STUDENT, User::ROLE_ADVISER])
                ->groupBy('role')
                ->pluck('count', 'role')
                ->map(fn ($count) => (int) $count);

            $adminStats = [
                'theses' => Thesis::count(),
                'postgrad_theses' => PostgradThesis::count(),
                'users' => User::count(),
                'students' => $roleCounts[User::ROLE_STUDENT] ?? 0,
                'advisers' => $roleCounts[User::ROLE_ADVISER] ?? 0,
                'pending' => $statusCounts['pending'] ?? 0,
                'approved' => $statusCounts['approved'] ?? 0,
                'rejected' => $statusCounts['rejected'] ?? 0,
            ];
        }

        return view('dashboard', [
            'thesisStats' => $thesisStats,
            'adviserStats' => $adviserStats,
            'adminStats' => $adminStats,
        ]);
    })->name('dashboard');

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
    Route::get('/theses', [ThesisTitleController::class, 'index'])->name('theses.index');
    Route::get('/theses/create', [ThesisTitleController::class, 'create'])->name('theses.create');
    Route::post('/theses', [ThesisTitleController::class, 'store'])->name('theses.store');
    Route::get('/theses/{thesisTitle}', [ThesisTitleController::class, 'show'])->name('theses.show');
    Route::get('/certificates', [ThesisTitleController::class, 'certificates'])->name('theses.certificates');

    Route::post('/theses/{thesisTitle}/uploads', [ThesisController::class, 'store'])
        ->name('theses.upload');

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

    Route::get('/theses/{thesis}/approval-sheet', [ThesisReviewController::class, 'approvalSheet'])
        ->middleware('can:downloadCertificate,thesis')
        ->name('theses.approval');

    Route::prefix('adviser')
        ->middleware(['role:adviser'])
        ->group(function () {
            Route::get('/theses', [ThesisTitleReviewController::class, 'index'])
                ->name('adviser.theses.index');

            Route::get('/theses/{thesisTitle}', [ThesisTitleReviewController::class, 'show'])
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

            Route::get('/postgrad/theses', [PostgradThesisController::class, 'index'])
                ->name('admin.postgrad.index');

            Route::get('/postgrad/theses/create', [PostgradThesisController::class, 'create'])
                ->name('admin.postgrad.create');

            Route::post('/postgrad/theses', [PostgradThesisController::class, 'store'])
                ->name('admin.postgrad.store');

            Route::get('/postgrad/theses/{postgradThesis}/download', [PostgradThesisController::class, 'download'])
                ->name('admin.postgrad.download');
        });
});

require __DIR__.'/auth.php';
