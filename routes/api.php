<?php

use App\Http\Controllers\Webhooks\CopyleaksWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/copyleaks/status', [CopyleaksWebhookController::class, 'status'])->name('copyleaks.webhooks.status');
Route::post('/copyleaks/completed', [CopyleaksWebhookController::class, 'completed'])->name('copyleaks.webhooks.completed');
Route::post('/copyleaks/export', [CopyleaksWebhookController::class, 'export'])->name('copyleaks.webhooks.export');
