<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| هنا يتم تعريف جميع routes الخاصة بلوحة تحكم الأدمن
| جميع هذه الـ routes محمية بـ auth:sanctum و admin middleware
| للتأكد من أن المستخدم مصادق عليه ولديه صلاحيات admin
|
*/

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    
    // لوحة تحكم الأدمن - Dashboard
    Route::prefix('dashboard')->group(function () {
        
        // عرض جميع الفواتير
        Route::get('/invoices', [DashboardController::class, 'allInvoices'])
            ->name('admin.dashboard.invoices');
        
        // فلترة الفواتير حسب التاريخ والمستخدم والحالة
        Route::get('/invoices/filter', [DashboardController::class, 'filterInvoices'])
            ->name('admin.dashboard.invoices.filter');
        
        // تقارير المبيعات اليومية والشهرية
        Route::get('/reports/sales', [DashboardController::class, 'salesReport'])
            ->name('admin.dashboard.reports.sales');
    });
    
});
