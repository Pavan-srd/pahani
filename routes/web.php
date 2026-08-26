<?php

use App\Http\Controllers\PahaniController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\ReportsController;

Route::get('/dashboard', [PahaniController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/', [PahaniController::class, 'index'])->name('pahani.index');

    Route::get('/pahani/file/{pahani}', [PahaniController::class, 'showFile'])->name('pahani.file');
    Route::get('/pahani/view', [PahaniController::class, 'view'])->name('pahani.view');
    Route::get('/pahani/pdf-source/{pahani}', [PahaniController::class, 'pdfSource'])->name('pahani.pdf-source');

    Route::middleware('verify-pdf-access')->group(function () {
        Route::get('/pahani/view-pdf/{pahani}', [PahaniController::class, 'viewPdfPage'])
            ->name('pahani.view-pdf');
        Route::post('/pahani/pdf-url/{pahani}', [PahaniController::class, 'getSignedUrl'])
            ->name('pahani.pdf-url');
        Route::get('/pahani/pdf-redirect/{pahani}', [PahaniController::class, 'redirectToCloudflare'])
            ->name('pahani.pdf-redirect');
        Route::post('/pahani/{pahani}/update-file', [PahaniController::class, 'updateFile'])->name('pahani.update-file');
    });
    
    Route::post('/pahani/presign', [PahaniController::class, 'presign'])->name('pahani.presign');
    Route::post('/pahani/store',   [PahaniController::class, 'store'])->name('pahani.store');

    Route::post('/pahani/multipart-init', [PahaniController::class, 'multipartInit'])->name('pahani.multipart.init');
    Route::post('/pahani/multipart-sign-part', [PahaniController::class, 'multipartSignPart'])->name('pahani.multipart.sign-part');
    Route::post('/pahani/multipart-complete', [PahaniController::class, 'multipartComplete'])->name('pahani.multipart.complete');
    Route::post('/pahani/multipart-abort', [PahaniController::class, 'multipartAbort'])->name('pahani.multipart.abort');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // AJAX: villages for a mandal
    Route::get('/api/mandals/{mandal}/villages', [PahaniController::class, 'apiVillages']);

    // AJAX: existing pahani records for a village
    Route::get('/api/villages/{village}/pahanis', [PahaniController::class, 'apiVillagePahanis']);

    Route::get('/reports/user', [ReportsController::class, 'userReport'])->name('reports.user');

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

Route::middleware('guest')->group(function () {
 
    // Forgot Password Routes
    Route::get('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetController::class, 'request'])
        ->name('password.request');
 
    Route::post('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetController::class, 'email'])
        ->name('password.email');
 
    // Reset Password Routes
    Route::get('/reset-password/{token}', [\App\Http\Controllers\Auth\PasswordResetController::class, 'reset'])
        ->name('password.reset');
 
    Route::post('/reset-password', [\App\Http\Controllers\Auth\PasswordResetController::class, 'update'])
        ->name('password.update');
});
/*
|--------------------------------------------------------------------------
| Protected Admin Routes
|--------------------------------------------------------------------------
| The `is_admin` middleware (registered in app/Http/Kernel.php, alias
| "is_admin") handles: not logged in → admin.login; logged in but not an
| admin → logged out + admin.login with an error; otherwise → allowed.
*/
Route::middleware(['is_admin'])->group(function () {
    // Route::get('/admindashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    // Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    Route::get('/admin', fn() => view('admin.mandals.index'))->name('admin.dashboard');
    Route::get('/admin/mandals', fn() => view('admin.mandals.index'))->name('admin.mandals.index');
    Route::get('/admin/villages', fn() => view('admin.villages.index'))->name('admin.villages.index');
    Route::get('/admin/working-offices', fn() => view('admin.working-offices.index'))->name('admin.working-offices.index');
    Route::get('/admin/users', [AdminController::class, 'usersList'])->name('admin.users.index');

    Route::get('/admin/profile', [AdminController::class, 'profile'])->name('admin.profile');

    // JSON list endpoints (sidebar tabs)
    Route::get('/api/admin/mandals', [AdminController::class, 'mandals']);
    Route::get('/api/admin/villages', [AdminController::class, 'villages']);
    Route::get('/api/admin/users', [AdminController::class, 'users']);

    Route::get('api/admin/users/{user}', [AdminController::class, 'show'])->name('admin.users.show');

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

    // Working Office endpoints
    Route::get('/api/admin/working-offices', [AdminController::class, 'workingOffices']);
    Route::post('/api/admin/working-offices', [AdminController::class, 'storeWorkingOffice']);
    Route::get('/api/admin/working-offices/{id}/edit', [AdminController::class, 'editWorkingOffice']);
    Route::put('/api/admin/working-offices/{id}', [AdminController::class, 'updateWorkingOffice']);
    Route::delete('/api/admin/working-offices/{id}', [AdminController::class, 'destroyWorkingOffice']);

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

    Route::get('/reports/admin', [ReportsController::class, 'adminReport'])->name('reports.admin');

});

require __DIR__.'/auth.php';