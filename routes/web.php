<?php

use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\changePasswordController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Welcome
Volt::route('/', 'welcome');

// Login
Volt::route('/login', 'login')->name('login');

//Logout
//Route::get('/logout', function () {
//    auth()->logout();
//    request()->session()->invalidate();
//    request()->session()->regenerateToken();
//
//    return redirect('/');
//});

Route::middleware('auth')->group(function () {
//    // home
    Volt::route('/home', 'dashboard.home');
//
//    // Users
    Volt::route('/users', 'users.index');
    Volt::route('/users/{user}/edit', 'users.edit');
    Volt::route('/users/create', 'users.create');
    Volt::route('/change-password', 'users.password');
    Volt::route('/users/{user}', 'users.show');
    Route::post('/users/create', [UserController::class, 'store'])->name('users.store');
  //Route::get('/users/{user}/userrole', [UserController::class, 'userrole'])->name('users.userrole');
  //Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
//
//    // Brands
//    Volt::route('/brands', 'brands.index');
//    Volt::route('/brands/{brand}/edit', 'brands.edit');
//    Volt::route('/brands/create', 'brands.create');
//
    // Categories
    Volt::route('/categories', 'categories.index');
    Volt::route('/categories/{category}/edit', 'categories.edit');
    Volt::route('/categories/create', 'categories.create');
//
//    // Products
//    Volt::route('/products', 'products.index');
//    Volt::route('/products/{product}/edit', 'products.edit');
//    Volt::route('/products/create', 'products.create');
//    
//    // Orders
//    Volt::route('/orders', 'orders.index');
//    Volt::route('/orders/{order}/edit', 'orders.edit');
//    Volt::route('/orders/create', 'orders.create');

//Route::get('change-password', [changePasswordController::class, 'index']);
Route::post('change-password', [changePasswordController::class, 'changePassword']);

//projects
Volt::route('/projects', 'projects.index');
Volt::route('/projects/create', 'projects.create');
Volt::route('/projects/{project}/edit', 'projects.edit');



//tasks
Volt::route('/tasks', 'tasks.index');



//role 

Route::get('roles', [RoleController::class, 'index'])->name('admin.roles.index');
Route::get('users/{user}/roles', [UserController::class, 'role'])->name('users.role');
Route::get('roles/create', [RoleController::class, 'create'])->name('admin.roles.create');
Route::post('roles', [RoleController::class, 'store'])->name('admin.roles.store');
Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->name('admin.roles.edit');
Route::put('roles/{role}', [RoleController::class, 'update'])->name('admin.roles.update');

//Permission
Route::get('permissions', [PermissionController::class, 'index'])->name('admin.permissions.index');
Route::get('permissions/create', [PermissionController::class, 'create'])->name('admin.permissions.create');
Route::post('permissions', [PermissionController::class, 'store'])->name('admin.permissions.store');
Route::get('permissions/{permission}/edit', [PermissionController::class, 'edit'])->name('admin.permissions.edit');
Route::put('permissions/{permission}', [PermissionController::class, 'update'])->name('admin.permissions.update');

Route::post('/roles/{role}/permissions', [RoleController::class, 'givePermission'])->name('admin.roles.permissions');
Route::delete('/roles/{role}/permissions/{permission}', [RoleController::class, 'revokePermission'])->name('admin.roles.permissions.revoke');
//Route::resource('/permissions', PermissionController::class);
Route::post('/permissions/{permission}/roles', [PermissionController::class, 'assignRole'])->name('admin.permissions.roles');
Route::delete('/permissions/{permission}/roles/{role}', [PermissionController::class, 'removeRole'])->name('admin.permissions.roles.remove');
Route::post('/users/{user}/roles', [UserController::class, 'assignRole'])->name('users.roles');
Route::delete('/users/{user}/roles/{role}', [UserController::class, 'removeRole'])->name('users.roles.remove');
Route::post('/users/{user}/permissions', [UserController::class, 'givePermission'])->name('users.permissions');
Route::delete('/users/{user}/permissions/{permission}', [UserController::class, 'revokePermission'])->name('users.permissions.revoke');
});