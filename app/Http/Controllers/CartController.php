<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    // ==================== NEW CART SYSTEM METHODS ====================

    /**
     * عرض محتويات السلة الخاصة بالمستخدم الحالي (authenticated user)
     */
    public function showUserCart(): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'المستخدم غير مصادق عليه'
                ], 401);
            }

            // الحصول على السلة النشطة للمستخدم أو إنشاء واحدة جديدة
            $cart = Cart::with(['cartItems.product'])
                ->where('user_id', $user->id)
                ->latest()
                ->first();

            if (!$cart) {
                // إنشاء سلة جديدة للمستخدم
                $cart = Cart::create(['user_id' => $user->id]);
                $cart->load(['cartItems.product']);
            }

            // حساب الإجمالي
            $total = $this->calculateCartTotal($cart);

            return response()->json([
                'success' => true,
                'data' => [
                    'cart' => $cart,
                    'total' => $total,
                    'items_count' => $cart->cartItems->sum('quantity')
                ],
                'message' => 'تم عرض محتويات السلة بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * إضافة منتج إلى السلة مع الكمية
     * لو المنتج موجود بالفعل في السلة يتم زيادة الكمية بدلاً من تكرار العنصر
     */
    public function addToCart(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'product_id' => 'required|integer|exists:products,id',
                'quantity' => 'required|integer|min:1'
            ]);

            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'المستخدم غير مصادق عليه'
                ], 401);
            }

            // الحصول على السلة النشطة للمستخدم أو إنشاء واحدة جديدة
            $cart = Cart::where('user_id', $user->id)->latest()->first();
            
            if (!$cart) {
                $cart = Cart::create(['user_id' => $user->id]);
            }

            // التحقق من توفر المنتج والمخزون
            $product = Product::find($validated['product_id']);
            
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'المنتج غير موجود'
                ], 404);
            }

            if ($product->stock < $validated['quantity']) {
                return response()->json([
                    'success' => false,
                    'message' => 'الكمية المطلوبة غير متوفرة في المخزون'
                ], 400);
            }

            // التحقق من وجود المنتج في السلة
            $existingItem = CartItem::where('cart_id', $cart->id)
                ->where('product_id', $validated['product_id'])
                ->first();

            if ($existingItem) {
                // زيادة الكمية إذا كان المنتج موجود
                $newQuantity = $existingItem->quantity + $validated['quantity'];
                
                if ($product->stock < $newQuantity) {
                    return response()->json([
                        'success' => false,
                        'message' => 'الكمية الإجمالية المطلوبة غير متوفرة في المخزون'
                    ], 400);
                }

                $existingItem->update(['quantity' => $newQuantity]);
                $cartItem = $existingItem->fresh();
            } else {
                // إضافة منتج جديد للسلة
                $cartItem = CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $validated['product_id'],
                    'quantity' => $validated['quantity']
                ]);
            }

            $cartItem->load('product');

            return response()->json([
                'success' => true,
                'data' => $cartItem,
                'message' => 'تم إضافة المنتج إلى السلة بنجاح'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في التحقق من البيانات',
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
     * تعديل الكمية لمنتج محدد داخل السلة
     */
    public function updateCartItem(int $id, Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'quantity' => 'required|integer|min:1'
            ]);

            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'المستخدم غير مصادق عليه'
                ], 401);
            }

            // البحث عن عنصر السلة
            $cartItem = CartItem::with('product')
                ->whereHas('cart', function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->find($id);

            if (!$cartItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'عنصر السلة غير موجود'
                ], 404);
            }

            // التحقق من توفر المخزون
            if ($cartItem->product->stock < $validated['quantity']) {
                return response()->json([
                    'success' => false,
                    'message' => 'الكمية المطلوبة غير متوفرة في المخزون'
                ], 400);
            }

            $cartItem->update(['quantity' => $validated['quantity']]);

            return response()->json([
                'success' => true,
                'data' => $cartItem->fresh(),
                'message' => 'تم تحديث كمية المنتج بنجاح'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في التحقق من البيانات',
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
     * حذف منتج من السلة
     */
    public function removeFromCart(int $id): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'المستخدم غير مصادق عليه'
                ], 401);
            }

            // البحث عن عنصر السلة
            $cartItem = CartItem::whereHas('cart', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })->find($id);

            if (!$cartItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'عنصر السلة غير موجود'
                ], 404);
            }

            $cartItem->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف المنتج من السلة بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * حساب إجمالي السلة
     */
    private function calculateCartTotal(Cart $cart): float
    {
        $total = 0;
        foreach ($cart->cartItems as $item) {
            $total += $item->quantity * $item->product->price;
        }
        return round($total, 2);
    }
}
