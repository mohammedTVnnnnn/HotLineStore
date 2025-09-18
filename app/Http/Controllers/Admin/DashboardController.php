<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * عرض جميع الفواتير مع بيانات المستخدم والمنتجات المرتبطة
     * 
     * @return JsonResponse
     */
    public function allInvoices(): JsonResponse
    {
        try {
            $invoices = $this->invoiceService->getAllInvoicesWithDetails();
            
            return response()->json([
                'success' => true,
                'data' => $invoices,
                'message' => 'تم عرض جميع الفواتير بنجاح'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في عرض الفواتير: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * فلترة الفواتير حسب التاريخ والمستخدم
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function filterInvoices(Request $request): JsonResponse
    {
        try {
            // التحقق من صحة البيانات
            $request->validate([
                'from' => 'nullable|date',
                'to' => 'nullable|date|after_or_equal:from',
                'user_id' => 'nullable|integer|exists:users,id',
                'status' => 'nullable|string|in:pending,completed,cancelled',
                'per_page' => 'nullable|integer|min:1|max:100'
            ]);

            $filters = [
                'from' => $request->input('from'),
                'to' => $request->input('to'),
                'user_id' => $request->input('user_id'),
                'status' => $request->input('status'),
                'per_page' => $request->input('per_page', 15)
            ];

            $invoices = $this->invoiceService->getFilteredInvoices($filters);
            
            return response()->json([
                'success' => true,
                'data' => $invoices,
                'message' => 'تم فلترة الفواتير بنجاح',
                'filters_applied' => array_filter($filters, function($value) {
                    return $value !== null && $value !== 15; // استبعاد القيم الافتراضية
                })
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في التحقق من البيانات',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في فلترة الفواتير: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * تقرير المبيعات اليومي أو الشهري
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function salesReport(Request $request): JsonResponse
    {
        try {
            // التحقق من صحة البيانات
            $request->validate([
                'type' => 'required|string|in:daily,monthly',
                'date' => 'nullable|date',
                'year' => 'nullable|integer|min:2020|max:2030',
                'month' => 'nullable|integer|min:1|max:12'
            ]);

            $type = $request->input('type');
            $date = $request->input('date');
            $year = $request->input('year', date('Y'));
            $month = $request->input('month', date('m'));

            $report = $this->invoiceService->getSalesReport($type, $date, $year, $month);
            
            return response()->json([
                'success' => true,
                'data' => $report,
                'message' => "تم إنشاء تقرير المبيعات {$type} بنجاح",
                'report_type' => $type,
                'period' => $this->getPeriodDescription($type, $date, $year, $month)
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في التحقق من البيانات',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في إنشاء تقرير المبيعات: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * الحصول على وصف الفترة الزمنية للتقرير
     * 
     * @param string $type
     * @param string|null $date
     * @param int $year
     * @param int $month
     * @return string
     */
    private function getPeriodDescription(string $type, ?string $date, int $year, int $month): string
    {
        if ($type === 'daily') {
            if ($date) {
                return Carbon::parse($date)->format('Y-m-d');
            }
            return Carbon::now()->format('Y-m-d');
        } else {
            return Carbon::create($year, $month, 1)->format('Y-m');
        }
    }
}
