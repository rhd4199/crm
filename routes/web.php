<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InteractionController;
use App\Http\Controllers\PipelineStageController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\ReportCustomerController;
use App\Http\Controllers\ReportEmployeeController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\menuControlling;

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Guest
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.perform');
});

// Auth + Company scope
Route::middleware(['auth', 'company'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Customers CRUD
    Route::resource('customers', CustomerController::class);

    // Interactions (tambah interaksi untuk satu customer)
    Route::post('customers/{customer}/stage', [CustomerController::class, 'updateStage'])
        ->name('customers.updateStage');

    Route::post('customers/{customer}/interactions', [InteractionController::class, 'store'])
        ->name('customers.interactions.store');

    // Pipeline / Roadmap
    Route::get('pipeline', [PipelineStageController::class, 'index'])->name('pipeline.index');
    Route::post('pipeline', [PipelineStageController::class, 'store'])->name('pipeline.store');
    Route::put('pipeline/{pipelineStage}', [PipelineStageController::class, 'update'])->name('pipeline.update');
    Route::delete('pipeline/{pipelineStage}', [PipelineStageController::class, 'destroy'])->name('pipeline.destroy');

    Route::resource('team', TeamController::class)->except(['show']);

    Route::get('reports/customers', [ReportCustomerController::class, 'index'])->name('reports.customers');
    Route::get('reports/employees', [ReportEmployeeController::class, 'index'])->name('reports.employees');

    // Perusahaan (super admin only)
    Route::resource('companies', CompanyController::class)->except(['show']);

    Route::get('customers/export/csv', [CustomerController::class, 'exportCsv'])
        ->name('customers.export.csv');

    Route::get('reports/customers/export/csv', [ReportCustomerController::class, 'exportCsv'])
        ->name('reports.customers.export.csv');

    Route::get('reports/employees/export/csv', [ReportEmployeeController::class, 'exportCsv'])
        ->name('reports.employees.export.csv');
    
    Route::get('setting/menu', [menuControlling::class, 'index'])->name('setting.menu');
        
});
