<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Display a listing of carts
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $carts = $this->cartService->getAllCarts($perPage);
            
            return response()->json([
                'success' => true,
                'data' => $carts,
                'message' => 'Carts retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified cart
     */
    public function show(int $id): JsonResponse
    {
        try {
            $cart = $this->cartService->getCartById($id);
            
            return response()->json([
                'success' => true,
                'data' => $cart,
                'message' => 'Cart retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }

    /**
     * Store a newly created cart
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|integer|exists:users,id'
            ]);

            $cart = $this->cartService->createCart($validated['user_id']);
            
            return response()->json([
                'success' => true,
                'data' => $cart,
                'message' => 'Cart created successfully'
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
     * Remove the specified cart
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->cartService->deleteCart($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Cart deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }

    /**
     * Get user's active cart
     */
    public function getUserCart(int $userId): JsonResponse
    {
        try {
            $cart = $this->cartService->getUserActiveCart($userId);
            
            return response()->json([
                'success' => true,
                'data' => $cart,
                'message' => $cart ? 'User cart retrieved successfully' : 'No active cart found'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add product to cart
     */
    public function addProduct(Request $request, int $cartId): JsonResponse
    {
        try {
            $validated = $request->validate([
                'product_id' => 'required|integer|exists:products,id',
                'quantity' => 'sometimes|integer|min:1'
            ]);

            $quantity = $validated['quantity'] ?? 1;
            $cartItem = $this->cartService->addProductToCart($cartId, $validated['product_id'], $quantity);
            
            return response()->json([
                'success' => true,
                'data' => $cartItem,
                'message' => 'Product added to cart successfully'
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
     * Update cart item quantity
     */
    public function updateItemQuantity(Request $request, int $cartItemId): JsonResponse
    {
        try {
            $validated = $request->validate([
                'quantity' => 'required|integer|min:1'
            ]);

            $cartItem = $this->cartService->updateCartItemQuantity($cartItemId, $validated['quantity']);
            
            return response()->json([
                'success' => true,
                'data' => $cartItem,
                'message' => 'Cart item quantity updated successfully'
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
     * Remove product from cart
     */
    public function removeProduct(int $cartItemId): JsonResponse
    {
        try {
            $this->cartService->removeProductFromCart($cartItemId);
            
            return response()->json([
                'success' => true,
                'message' => 'Product removed from cart successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }

    /**
     * Calculate cart total
     */
    public function calculateTotal(int $cartId): JsonResponse
    {
        try {
            $total = $this->cartService->calculateCartTotal($cartId);
            
            return response()->json([
                'success' => true,
                'data' => ['total' => $total],
                'message' => 'Cart total calculated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }

    /**
     * Clear cart
     */
    public function clear(int $cartId): JsonResponse
    {
        try {
            $this->cartService->clearCart($cartId);
            
            return response()->json([
                'success' => true,
                'message' => 'Cart cleared successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }

    /**
     * Get cart items count
     */
    public function getItemsCount(int $cartId): JsonResponse
    {
        try {
            $count = $this->cartService->getCartItemsCount($cartId);
            
            return response()->json([
                'success' => true,
                'data' => ['count' => $count],
                'message' => 'Cart items count retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }

    /**
     * Validate cart for checkout
     */
    public function validateForCheckout(int $cartId): JsonResponse
    {
        try {
            $errors = $this->cartService->validateCartForCheckout($cartId);
            
            return response()->json([
                'success' => empty($errors),
                'data' => ['errors' => $errors],
                'message' => empty($errors) ? 'Cart is valid for checkout' : 'Cart validation failed'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }
}
