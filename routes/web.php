<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Storage;
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', fn () => redirect()->route('customers.index'))->name('dashboard');
    Route::resource('customers', CustomerController::class);
    Route::resource('users', UserController::class);
    // Serve only generated avatars; no symlink or shell access is required.
    Route::get('/storage/avatars/{filename}', function (string $filename) {
        abort_unless(preg_match('/^user-[0-9]+-[a-f0-9]{16}\\.jpg$/D', $filename), 404);
        abort_unless(Storage::disk('public')->exists('avatars/'.$filename), 404);
        return Storage::disk('public')->response('avatars/'.$filename, null, ['X-Content-Type-Options'=>'nosniff']);
    })->name('avatars.show');
});
require __DIR__.'/auth.php';
