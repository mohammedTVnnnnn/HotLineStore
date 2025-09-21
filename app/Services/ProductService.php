<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductService
{
    /**
     * Get all products with pagination
     */
    public function getAllProducts(int $perPage = 15): LengthAwarePaginator
    {
        return Product::with('category')->paginate($perPage);
    }

    /**
     * Get a specific product by ID
     */
    public function getProductById(int $id): Product
    {
        $product = Product::with('category')->find($id);
        
        if (!$product) {
            throw new \Exception('Product not found', 404);
        }
        
        return $product;
    }

    /**
     * Create a new product
     */
    public function createProduct(array $data): Product
    {
        // Handle image upload if present
        if (isset($data['image']) && $data['image']) {
            $data['image'] = $this->handleImageUpload($data['image']);
        }

        return Product::create($data);
    }

    /**
     * Update an existing product
     */
    public function updateProduct(int $id, array $data): Product
    {
        $product = $this->getProductById($id);
        
        // Handle image upload if present
        if (isset($data['image']) && $data['image']) {
            // Delete old image if exists
            if ($product->image) {
                $this->deleteImage($product->image);
            }
            $data['image'] = $this->handleImageUpload($data['image']);
        }
        
        $product->update($data);
        
        return $product->fresh();
    }

    /**
     * Delete a product
     */
    public function deleteProduct(int $id): bool
    {
        $product = $this->getProductById($id);
        
        return $product->delete();
    }

    /**
     * Get products with low stock
     */
    public function getLowStockProducts(int $threshold = 10): Collection
    {
        return Product::where('stock', '<=', $threshold)->get();
    }

    /**
     * Update product stock
     */
    public function updateStock(int $id, int $quantity): Product
    {
        $product = $this->getProductById($id);
        
        $product->update(['stock' => $quantity]);
        
        return $product->fresh();
    }

    /**
     * Search products by name or description
     */
    public function searchProducts(string $query, int $perPage = 15): LengthAwarePaginator
    {
        return Product::with('category')
            ->where('name', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->paginate($perPage);
    }

    /**
     * Get products by price range
     */
    public function getProductsByPriceRange(float $minPrice, float $maxPrice): Collection
    {
        return Product::with('category')
            ->whereBetween('price', [$minPrice, $maxPrice])
            ->get();
    }

    /**
     * Get products by category
     */
    public function getProductsByCategory(int $categoryId, int $perPage = 15): LengthAwarePaginator
    {
        return Product::with('category')
            ->where('category_id', $categoryId)
            ->paginate($perPage);
    }

    /**
     * Get products by category slug
     */
    public function getProductsByCategorySlug(string $slug, int $perPage = 15): LengthAwarePaginator
    {
        return Product::with('category')
            ->whereHas('category', function ($query) use ($slug) {
                $query->where('slug', $slug);
            })
            ->paginate($perPage);
    }

    /**
     * Get products with categories
     */
    public function getProductsWithCategories(int $perPage = 15): LengthAwarePaginator
    {
        return Product::with(['category.parent'])
            ->paginate($perPage);
    }

    /**
     * Search products by category
     */
    public function searchProductsByCategory(string $query, int $categoryId, int $perPage = 15): LengthAwarePaginator
    {
        return Product::with('category')
            ->where('category_id', $categoryId)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->paginate($perPage);
    }

    /**
     * Get products by multiple categories
     */
    public function getProductsByCategories(array $categoryIds, int $perPage = 15): LengthAwarePaginator
    {
        return Product::with('category')
            ->whereIn('category_id', $categoryIds)
            ->paginate($perPage);
    }

    /**
     * Get products without category
     */
    public function getProductsWithoutCategory(int $perPage = 15): LengthAwarePaginator
    {
        return Product::with('category')
            ->whereNull('category_id')
            ->paginate($perPage);
    }

    /**
     * Handle image upload
     */
    private function handleImageUpload($image): string
    {
        return $image->store('products', 'public');
    }

    /**
     * Delete image file
     */
    private function deleteImage(string $imagePath): void
    {
        $fullPath = storage_path('app/public/' . $imagePath);
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }
}
