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
}
