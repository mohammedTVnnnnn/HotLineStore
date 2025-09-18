<?php

namespace App\Http\Controllers;

use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class InvoiceController extends Controller
{
    protected InvoiceService $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * Display a listing of invoices
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $invoices = $this->invoiceService->getAllInvoices($perPage);
            
            return response()->json([
                'success' => true,
                'data' => $invoices,
                'message' => 'Invoices retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified invoice
     */
    public function show(int $id): JsonResponse
    {
        try {
            $invoice = $this->invoiceService->getInvoiceById($id);
            
            return response()->json([
                'success' => true,
                'data' => $invoice,
                'message' => 'Invoice retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }

    /**
     * Create invoice from cart
     */
    public function createFromCart(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'cart_id' => 'required|integer|exists:carts,id',
                'status' => 'sometimes|string|in:pending,completed,cancelled'
            ]);

            $status = $validated['status'] ?? 'pending';
            $invoice = $this->invoiceService->createInvoiceFromCart($validated['cart_id'], $status);
            
            return response()->json([
                'success' => true,
                'data' => $invoice,
                'message' => 'Invoice created from cart successfully'
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }

    /**
     * Create invoice manually
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|integer|exists:users,id',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|integer|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.price' => 'required|numeric|min:0',
                'status' => 'sometimes|string|in:pending,completed,cancelled'
            ]);

            $status = $validated['status'] ?? 'pending';
            $invoice = $this->invoiceService->createInvoice($validated['user_id'], $validated['items'], $status);
            
            return response()->json([
                'success' => true,
                'data' => $invoice,
                'message' => 'Invoice created successfully'
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }

    /**
     * Update invoice status
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'status' => 'required|string|in:pending,completed,cancelled'
            ]);

            $invoice = $this->invoiceService->updateInvoiceStatus($id, $validated['status']);
            
            return response()->json([
                'success' => true,
                'data' => $invoice,
                'message' => 'Invoice status updated successfully'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }

    /**
     * Remove the specified invoice
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->invoiceService->deleteInvoice($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Invoice deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }

    /**
     * Get user's invoices
     */
    public function getUserInvoices(int $userId): JsonResponse
    {
        try {
            $invoices = $this->invoiceService->getUserInvoices($userId);
            
            return response()->json([
                'success' => true,
                'data' => $invoices,
                'message' => 'User invoices retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get invoices by status
     */
    public function getByStatus(string $status): JsonResponse
    {
        try {
            $invoices = $this->invoiceService->getInvoicesByStatus($status);
            
            return response()->json([
                'success' => true,
                'data' => $invoices,
                'message' => "Invoices with status '{$status}' retrieved successfully"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get invoices by date range
     */
    public function getByDateRange(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date'
            ]);

            $invoices = $this->invoiceService->getInvoicesByDateRange(
                $validated['start_date'], 
                $validated['end_date']
            );
            
            return response()->json([
                'success' => true,
                'data' => $invoices,
                'message' => 'Invoices filtered by date range successfully'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get sales statistics
     */
    public function getSalesStatistics(Request $request): JsonResponse
    {
        try {
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
            
            $statistics = $this->invoiceService->getSalesStatistics($startDate, $endDate);
            
            return response()->json([
                'success' => true,
                'data' => $statistics,
                'message' => 'Sales statistics retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate total sales
     */
    public function calculateTotalSales(Request $request): JsonResponse
    {
        try {
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
            
            $totalSales = $this->invoiceService->calculateTotalSales($startDate, $endDate);
            
            return response()->json([
                'success' => true,
                'data' => ['total_sales' => $totalSales],
                'message' => 'Total sales calculated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
