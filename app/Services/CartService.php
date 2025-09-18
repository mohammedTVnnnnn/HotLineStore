<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CartService
{
    /**
     * Get all carts with pagination
     */
    public function getAllCarts(int $perPage = 15): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return Cart::with(['user', 'cartItems.product'])->paginate($perPage);
    }

    /**
     * Get a specific cart by ID
     */
    public function getCartById(int $id): Cart
    {
        $cart = Cart::with(['user', 'cartItems.product'])->find($id);
        
        if (!$cart) {
            throw new \Exception('Cart not found', 404);
        }
        
        return $cart;
    }

    /**
     * Create a new cart for a user
     */
    public function createCart(int $userId): Cart
    {
        // Check if user exists
        $user = User::find($userId);
        if (!$user) {
            throw new \Exception('User not found', 404);
        }

        return Cart::create(['user_id' => $userId]);
    }

    /**
     * Delete a cart
     */
    public function deleteCart(int $id): bool
    {
        $cart = $this->getCartById($id);
        
        return $cart->delete();
    }

    /**
     * Get user's active cart
     */
    public function getUserActiveCart(int $userId): ?Cart
    {
        return Cart::with(['cartItems.product'])
            ->where('user_id', $userId)
            ->latest()
            ->first();
    }

    /**
     * Add product to cart
     */
    public function addProductToCart(int $cartId, int $productId, int $quantity = 1): CartItem
    {
        $cart = $this->getCartById($cartId);
        $product = Product::find($productId);
        
        if (!$product) {
            throw new \Exception('Product not found', 404);
        }

        if ($product->stock < $quantity) {
            throw new \Exception('Insufficient stock', 400);
        }

        // Check if product already exists in cart
        $existingItem = CartItem::where('cart_id', $cartId)
            ->where('product_id', $productId)
            ->first();

        if ($existingItem) {
            // Update quantity
            $existingItem->update(['quantity' => $existingItem->quantity + $quantity]);
            return $existingItem;
        } else {
            // Create new cart item
            return CartItem::create([
                'cart_id' => $cartId,
                'product_id' => $productId,
                'quantity' => $quantity
            ]);
        }
    }

    /**
     * Update cart item quantity
     */
    public function updateCartItemQuantity(int $cartItemId, int $quantity): CartItem
    {
        $cartItem = CartItem::find($cartItemId);
        
        if (!$cartItem) {
            throw new \Exception('Cart item not found', 404);
        }

        if ($quantity <= 0) {
            throw new \Exception('Quantity must be greater than 0', 400);
        }

        if ($cartItem->product->stock < $quantity) {
            throw new \Exception('Insufficient stock', 400);
        }

        $cartItem->update(['quantity' => $quantity]);
        
        return $cartItem->fresh();
    }

    /**
     * Remove product from cart
     */
    public function removeProductFromCart(int $cartItemId): bool
    {
        $cartItem = CartItem::find($cartItemId);
        
        if (!$cartItem) {
            throw new \Exception('Cart item not found', 404);
        }

        return $cartItem->delete();
    }

    /**
     * Calculate cart total
     */
    public function calculateCartTotal(int $cartId): float
    {
        $cart = $this->getCartById($cartId);
        
        $total = 0;
        foreach ($cart->cartItems as $item) {
            $total += $item->quantity * $item->product->price;
        }
        
        return round($total, 2);
    }

    /**
     * Clear all items from cart
     */
    public function clearCart(int $cartId): bool
    {
        $cart = $this->getCartById($cartId);
        
        return CartItem::where('cart_id', $cartId)->delete() >= 0;
    }

    /**
     * Get cart items count
     */
    public function getCartItemsCount(int $cartId): int
    {
        return CartItem::where('cart_id', $cartId)->sum('quantity');
    }

    /**
     * Validate cart before checkout
     */
    public function validateCartForCheckout(int $cartId): array
    {
        $cart = $this->getCartById($cartId);
        $errors = [];

        foreach ($cart->cartItems as $item) {
            if ($item->product->stock < $item->quantity) {
                $errors[] = "Insufficient stock for product: {$item->product->name}";
            }
        }

        return $errors;
    }
}
