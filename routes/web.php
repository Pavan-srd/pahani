<?php

use App\Http\Controllers\PahaniController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminAuthController;

Route::get('/dashboard', [PahaniController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/', [PahaniController::class, 'index'])->name('pahani.index');

    Route::get('/pahani/view', [PahaniController::class, 'view'])->name('pahani.view');
    Route::get('/pahani/file/{pahani}', [PahaniController::class, 'showFile'])->name('pahani.file');

    Route::get('/pahani/view-pdf/{pahani}', [PahaniController::class, 'viewPdfPage'])->name('pahani.view-pdf');
    Route::get('/pahani/pdf-source/{pahani}', [PahaniController::class, 'pdfSource'])->name('pahani.pdf-source');

    Route::post('/store', [PahaniController::class, 'store'])->name('pahani.store');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // AJAX: villages for a mandal
    Route::get('/api/mandals/{mandal}/villages', [PahaniController::class, 'apiVillages']);

    // AJAX: existing pahani records for a village
    Route::get('/api/villages/{village}/pahanis', [PahaniController::class, 'apiVillagePahanis']);

    
});

/*
|--------------------------------------------------------------------------
| Admin Authentication (separate from the Breeze user login above)
|--------------------------------------------------------------------------
| Uses the same `users` table + `is_admin` flag rather than a separate
| guard/table. Visiting any /admin/* route while unauthenticated, or while
| authenticated as a non-admin, redirects here.
*/
Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

/*
|--------------------------------------------------------------------------
| Protected Admin Routes
|--------------------------------------------------------------------------
| The `is_admin` middleware (registered in app/Http/Kernel.php, alias
| "is_admin") handles: not logged in → admin.login; logged in but not an
| admin → logged out + admin.login with an error; otherwise → allowed.
*/
Route::middleware(['is_admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/profile', [AdminController::class, 'profile'])->name('admin.profile');

    // JSON list endpoints (sidebar tabs)
    Route::get('/api/admin/mandals', [AdminController::class, 'mandals']);
    Route::get('/api/admin/villages', [AdminController::class, 'villages']);
    Route::get('/api/admin/users', [AdminController::class, 'users']);

    // Add Mandal / Add Village (modal forms on the dashboard)
    Route::post('/api/admin/mandals', [AdminController::class, 'storeMandal']);
    Route::post('/api/admin/villages', [AdminController::class, 'storeVillage']);

    // Toggle active/inactive
    Route::patch('/api/admin/mandals/{mandal}/toggle-status', [AdminController::class, 'toggleMandalStatus']);
    Route::patch('/api/admin/villages/{village}/toggle-status', [AdminController::class, 'toggleVillageStatus']);
    Route::patch('/api/admin/users/{user}/toggle-status', [AdminController::class, 'toggleUserStatus']);

    // Edit (prefill for modal)
    Route::get('/api/admin/mandals/{mandal}/edit', [AdminController::class, 'editMandal']);
    Route::get('/api/admin/villages/{village}/edit', [AdminController::class, 'editVillage']);
    Route::get('/api/admin/users/{user}/edit', [AdminController::class, 'editUser']);

    // Update
    Route::put('/api/admin/mandals/{mandal}', [AdminController::class, 'updateMandal']);
    Route::put('/api/admin/villages/{village}', [AdminController::class, 'updateVillage']);
    Route::put('/api/admin/users/{user}', [AdminController::class, 'updateUser']);

    // Delete
    Route::delete('/api/admin/mandals/{mandal}', [AdminController::class, 'destroyMandal']);
    Route::delete('/api/admin/villages/{village}', [AdminController::class, 'destroyVillage']);
    Route::delete('/api/admin/users/{user}', [AdminController::class, 'destroyUser']);

    Route::prefix('admin/pahani-management')->name('admin.pahani-management.')->group(function () {
        
        // List all PDFs (admin view)
        Route::get('/', [\App\Http\Controllers\Admin\PahaniManagementController::class, 'index'])
            ->name('index');

        // Show details of a single PDF
        Route::get('/{pahani}', [\App\Http\Controllers\Admin\PahaniManagementController::class, 'show'])
            ->name('show');

        // Delete a single PDF
        Route::delete('/{pahani}', [\App\Http\Controllers\Admin\PahaniManagementController::class, 'destroy'])
            ->name('destroy');

        // Bulk delete PDFs
        Route::post('/bulk-delete', [\App\Http\Controllers\Admin\PahaniManagementController::class, 'bulkDelete'])
            ->name('bulk-delete');

        // Export PDFs list as CSV
        Route::get('/export', [\App\Http\Controllers\Admin\PahaniManagementController::class, 'export'])
            ->name('export');
    });
});

require __DIR__.'/auth.php';