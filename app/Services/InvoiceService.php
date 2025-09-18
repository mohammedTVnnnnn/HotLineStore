<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    /**
     * Get all invoices with pagination
     */
    public function getAllInvoices(int $perPage = 15): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return Invoice::with(['user', 'invoiceItems.product'])->paginate($perPage);
    }

    /**
     * Get a specific invoice by ID
     */
    public function getInvoiceById(int $id): Invoice
    {
        $invoice = Invoice::with(['user', 'invoiceItems.product'])->find($id);
        
        if (!$invoice) {
            throw new \Exception('Invoice not found', 404);
        }
        
        return $invoice;
    }

    /**
     * Create invoice from cart
     */
    public function createInvoiceFromCart(int $cartId, string $status = 'pending'): Invoice
    {
        return DB::transaction(function () use ($cartId, $status) {
            $cart = Cart::with(['cartItems.product', 'user'])->find($cartId);
            
            if (!$cart) {
                throw new \Exception('Cart not found', 404);
            }

            if ($cart->cartItems->isEmpty()) {
                throw new \Exception('Cart is empty', 400);
            }

            // Validate stock availability
            foreach ($cart->cartItems as $item) {
                if ($item->product->stock < $item->quantity) {
                    throw new \Exception("Insufficient stock for product: {$item->product->name}", 400);
                }
            }

            // Calculate total
            $total = 0;
            foreach ($cart->cartItems as $item) {
                $total += $item->quantity * $item->product->price;
            }

            // Create invoice
            $invoice = Invoice::create([
                'user_id' => $cart->user_id,
                'total' => round($total, 2),
                'status' => $status
            ]);

            // Create invoice items and update stock
            foreach ($cart->cartItems as $cartItem) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->product->price
                ]);

                // Update product stock
                $cartItem->product->decrement('stock', $cartItem->quantity);
            }

            // Clear cart
            $cart->cartItems()->delete();

            return $invoice->load(['user', 'invoiceItems.product']);
        });
    }

    /**
     * Create invoice manually
     */
    public function createInvoice(int $userId, array $items, string $status = 'pending'): Invoice
    {
        return DB::transaction(function () use ($userId, $items, $status) {
            $user = User::find($userId);
            
            if (!$user) {
                throw new \Exception('User not found', 404);
            }

            $total = 0;
            $invoiceItems = [];

            // Validate items and calculate total
            foreach ($items as $item) {
                $product = Product::find($item['product_id']);
                
                if (!$product) {
                    throw new \Exception("Product not found: {$item['product_id']}", 404);
                }

                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Insufficient stock for product: {$product->name}", 400);
                }

                $itemTotal = $item['quantity'] * $item['price'];
                $total += $itemTotal;

                $invoiceItems[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ];
            }

            // Create invoice
            $invoice = Invoice::create([
                'user_id' => $userId,
                'total' => round($total, 2),
                'status' => $status
            ]);

            // Create invoice items and update stock
            foreach ($invoiceItems as $itemData) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $itemData['product_id'],
                    'quantity' => $itemData['quantity'],
                    'price' => $itemData['price']
                ]);

                Product::find($itemData['product_id'])->decrement('stock', $itemData['quantity']);
            }

            return $invoice->load(['user', 'invoiceItems.product']);
        });
    }

    /**
     * Update invoice status
     */
    public function updateInvoiceStatus(int $id, string $status): Invoice
    {
        $invoice = $this->getInvoiceById($id);
        
        $invoice->update(['status' => $status]);
        
        return $invoice->fresh();
    }

    /**
     * Delete an invoice
     */
    public function deleteInvoice(int $id): bool
    {
        $invoice = $this->getInvoiceById($id);
        
        return $invoice->delete();
    }

    /**
     * Get user's invoices
     */
    public function getUserInvoices(int $userId): Collection
    {
        return Invoice::with(['invoiceItems.product'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get invoices by status
     */
    public function getInvoicesByStatus(string $status): Collection
    {
        return Invoice::with(['user', 'invoiceItems.product'])
            ->where('status', $status)
            ->get();
    }

    /**
     * Get invoices by date range
     */
    public function getInvoicesByDateRange(string $startDate, string $endDate): Collection
    {
        return Invoice::with(['user', 'invoiceItems.product'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();
    }

    /**
     * Calculate total sales
     */
    public function calculateTotalSales(string $startDate = null, string $endDate = null): float
    {
        $query = Invoice::where('status', 'completed');
        
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        return round($query->sum('total'), 2);
    }

    /**
     * Get sales statistics
     */
    public function getSalesStatistics(string $startDate = null, string $endDate = null): array
    {
        $query = Invoice::query();
        
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        return [
            'total_sales' => round($query->sum('total'), 2),
            'total_invoices' => $query->count(),
            'average_invoice_value' => round($query->avg('total'), 2),
            'pending_invoices' => $query->where('status', 'pending')->count(),
            'completed_invoices' => $query->where('status', 'completed')->count(),
            'cancelled_invoices' => $query->where('status', 'cancelled')->count(),
        ];
    }

    // ==================== NEW INVOICE SYSTEM METHODS ====================

    /**
     * إنشاء فاتورة من السلة النشطة للمستخدم الحالي
     */
    public function createInvoiceFromUserCart(int $userId, string $status = 'completed'): Invoice
    {
        return DB::transaction(function () use ($userId, $status) {
            // الحصول على السلة النشطة للمستخدم
            $cart = Cart::with(['cartItems.product'])
                ->where('user_id', $userId)
                ->latest()
                ->first();
            
            if (!$cart) {
                throw new \Exception('لا توجد سلة نشطة للمستخدم', 404);
            }

            if ($cart->cartItems->isEmpty()) {
                throw new \Exception('السلة فارغة، لا يمكن إنشاء فاتورة', 400);
            }

            // التحقق من توفر المخزون لجميع المنتجات
            foreach ($cart->cartItems as $item) {
                if ($item->product->stock < $item->quantity) {
                    throw new \Exception("الكمية المطلوبة غير متوفرة للمنتج: {$item->product->name}", 400);
                }
            }

            // حساب الإجمالي
            $total = 0;
            foreach ($cart->cartItems as $item) {
                $total += $item->quantity * $item->product->price;
            }

            // إنشاء الفاتورة
            $invoice = Invoice::create([
                'user_id' => $userId,
                'total' => round($total, 2),
                'status' => $status
            ]);

            // نسخ المنتجات من السلة إلى الفاتورة وتحديث المخزون
            foreach ($cart->cartItems as $cartItem) {
                // إنشاء عنصر الفاتورة
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->product->price
                ]);

                // تقليل المخزون
                $cartItem->product->decrement('stock', $cartItem->quantity);
            }

            // تفريغ السلة
            $cart->cartItems()->delete();

            return $invoice->load(['user', 'invoiceItems.product']);
        });
    }

    /**
     * الحصول على فواتير المستخدم مع pagination
     */
    public function getUserInvoicesPaginated(int $userId, int $perPage = 15): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return Invoice::with(['invoiceItems.product'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * الحصول على فاتورة محددة للمستخدم الحالي
     */
    public function getUserInvoiceById(int $userId, int $invoiceId): Invoice
    {
        $invoice = Invoice::with(['user', 'invoiceItems.product'])
            ->where('user_id', $userId)
            ->find($invoiceId);
        
        if (!$invoice) {
            throw new \Exception('الفاتورة غير موجودة أو لا تنتمي للمستخدم', 404);
        }
        
        return $invoice;
    }

    /**
     * الحصول على جميع الفواتير مع تفاصيل كاملة للأدمن
     */
    public function getAllInvoicesWithDetails(int $perPage = 15): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return Invoice::with(['user', 'invoiceItems.product'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * فلترة الفواتير حسب المعايير المحددة
     */
    public function getFilteredInvoices(array $filters): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Invoice::with(['user', 'invoiceItems.product']);

        // فلترة حسب التاريخ
        if (!empty($filters['from'])) {
            $query->whereDate('created_at', '>=', $filters['from']);
        }
        
        if (!empty($filters['to'])) {
            $query->whereDate('created_at', '<=', $filters['to']);
        }

        // فلترة حسب المستخدم
        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        // فلترة حسب حالة الفاتورة
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('created_at', 'desc')
                    ->paginate($filters['per_page'] ?? 15);
    }

    /**
     * تقرير المبيعات اليومي أو الشهري
     */
    public function getSalesReport(string $type, ?string $date = null, int $year = null, int $month = null): array
    {
        $query = Invoice::where('status', 'completed');

        if ($type === 'daily') {
            $targetDate = $date ? \Carbon\Carbon::parse($date) : \Carbon\Carbon::now();
            $query->whereDate('created_at', $targetDate->format('Y-m-d'));
            $period = $targetDate->format('Y-m-d');
        } else {
            $targetYear = $year ?? date('Y');
            $targetMonth = $month ?? date('m');
            $query->whereYear('created_at', $targetYear)
                  ->whereMonth('created_at', $targetMonth);
            $period = $targetYear . '-' . str_pad($targetMonth, 2, '0', STR_PAD_LEFT);
        }

        // إحصائيات المبيعات
        $totalInvoices = $query->count();
        $totalSales = $query->sum('total');
        $uniqueCustomers = $query->distinct('user_id')->count('user_id');

        // متوسط قيمة الفاتورة
        $averageInvoiceValue = $totalInvoices > 0 ? round($totalSales / $totalInvoices, 2) : 0;

        // أفضل العملاء (أعلى إنفاق)
        $topCustomers = Invoice::with('user')
            ->where('status', 'completed')
            ->when($type === 'daily', function($q) use ($date) {
                $targetDate = $date ? \Carbon\Carbon::parse($date) : \Carbon\Carbon::now();
                return $q->whereDate('created_at', $targetDate->format('Y-m-d'));
            })
            ->when($type === 'monthly', function($q) use ($year, $month) {
                $targetYear = $year ?? date('Y');
                $targetMonth = $month ?? date('m');
                return $q->whereYear('created_at', $targetYear)
                         ->whereMonth('created_at', $targetMonth);
            })
            ->select('user_id', DB::raw('SUM(total) as total_spent'), DB::raw('COUNT(*) as invoice_count'))
            ->groupBy('user_id')
            ->orderBy('total_spent', 'desc')
            ->limit(5)
            ->get();

        // المنتجات الأكثر مبيعاً
        $topProducts = InvoiceItem::with('product')
            ->whereHas('invoice', function($q) use ($type, $date, $year, $month) {
                $q->where('status', 'completed');
                if ($type === 'daily') {
                    $targetDate = $date ? \Carbon\Carbon::parse($date) : \Carbon\Carbon::now();
                    $q->whereDate('created_at', $targetDate->format('Y-m-d'));
                } else {
                    $targetYear = $year ?? date('Y');
                    $targetMonth = $month ?? date('m');
                    $q->whereYear('created_at', $targetYear)
                      ->whereMonth('created_at', $targetMonth);
                }
            })
            ->select('product_id', DB::raw('SUM(quantity) as total_quantity'), DB::raw('SUM(quantity * price) as total_revenue'))
            ->groupBy('product_id')
            ->orderBy('total_quantity', 'desc')
            ->limit(5)
            ->get();

        return [
            'period' => $period,
            'type' => $type,
            'summary' => [
                'total_invoices' => $totalInvoices,
                'total_sales' => round($totalSales, 2),
                'unique_customers' => $uniqueCustomers,
                'average_invoice_value' => $averageInvoiceValue
            ],
            'top_customers' => $topCustomers,
            'top_products' => $topProducts,
            'generated_at' => now()->format('Y-m-d H:i:s')
        ];
    }
}
