<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Display a listing of products
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $products = $this->productService->getAllProducts($perPage);
            
            return response()->json([
                'success' => true,
                'data' => $products,
                'message' => 'Products retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified product
     */
    public function show(int $id): JsonResponse
    {
        try {
            $product = $this->productService->getProductById($id);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'price' => $product->price,
                    'stock' => $product->stock,
                    'image' => $product->image,
                    'image_url' => $product->image_url,
                    'created_at' => $product->created_at,
                    'updated_at' => $product->updated_at
                ],
                'message' => 'Product retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }

    /**
     * Store a newly created product
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);

            $product = $this->productService->createProduct($validated);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'price' => $product->price,
                    'stock' => $product->stock,
                    'image' => $product->image,
                    'image_url' => $product->image_url,
                    'created_at' => $product->created_at,
                    'updated_at' => $product->updated_at
                ],
                'message' => 'Product created successfully'
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
            ], 500);
        }
    }

    /**
     * Update the specified product
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'price' => 'sometimes|numeric|min:0',
                'stock' => 'sometimes|integer|min:0',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);

            $product = $this->productService->updateProduct($id, $validated);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'price' => $product->price,
                    'stock' => $product->stock,
                    'image' => $product->image,
                    'image_url' => $product->image_url,
                    'created_at' => $product->created_at,
                    'updated_at' => $product->updated_at
                ],
                'message' => 'Product updated successfully'
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
     * Remove the specified product
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->productService->deleteProduct($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }

    /**
     * Search products
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'query' => 'required|string|min:1',
                'per_page' => 'sometimes|integer|min:1|max:100'
            ]);

            $perPage = $validated['per_page'] ?? 15;
            $products = $this->productService->searchProducts($validated['query'], $perPage);
            
            return response()->json([
                'success' => true,
                'data' => $products,
                'message' => 'Search results retrieved successfully'
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
     * Get products by price range
     */
    public function getByPriceRange(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'min_price' => 'required|numeric|min:0',
                'max_price' => 'required|numeric|min:0|gte:min_price'
            ]);

            $products = $this->productService->getProductsByPriceRange(
                $validated['min_price'], 
                $validated['max_price']
            );
            
            return response()->json([
                'success' => true,
                'data' => $products,
                'message' => 'Products filtered by price range successfully'
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
     * Get low stock products
     */
    public function getLowStock(Request $request): JsonResponse
    {
        try {
            $threshold = $request->get('threshold', 10);
            $products = $this->productService->getLowStockProducts($threshold);
            
            return response()->json([
                'success' => true,
                'data' => $products,
                'message' => 'Low stock products retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update product stock
     */
    public function updateStock(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'stock' => 'required|integer|min:0'
            ]);

            $product = $this->productService->updateStock($id, $validated['stock']);
            
            return response()->json([
                'success' => true,
                'data' => $product,
                'message' => 'Product stock updated successfully'
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
     * Get products by category
     */
    public function getByCategory(Request $request, int $categoryId): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $products = $this->productService->getProductsByCategory($categoryId, $perPage);
            
            return response()->json([
                'success' => true,
                'data' => $products,
                'message' => 'Products retrieved by category successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get products by category slug
     */
    public function getByCategorySlug(Request $request, string $slug): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $products = $this->productService->getProductsByCategorySlug($slug, $perPage);
            
            return response()->json([
                'success' => true,
                'data' => $products,
                'message' => 'Products retrieved by category slug successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get products with categories
     */
    public function getWithCategories(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $products = $this->productService->getProductsWithCategories($perPage);
            
            return response()->json([
                'success' => true,
                'data' => $products,
                'message' => 'Products with categories retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search products by category
     */
    public function searchByCategory(Request $request, int $categoryId): JsonResponse
    {
        try {
            $query = $request->get('query');
            $perPage = $request->get('per_page', 15);

            if (empty($query)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Search query is required'
                ], 400);
            }

            $products = $this->productService->searchProductsByCategory($query, $categoryId, $perPage);
            
            return response()->json([
                'success' => true,
                'data' => $products,
                'message' => 'Products searched by category successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get products by multiple categories
     */
    public function getByCategories(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'category_ids' => 'required|array',
                'category_ids.*' => 'integer|exists:categories,id',
                'per_page' => 'integer|min:1|max:100'
            ]);

            $perPage = $validated['per_page'] ?? 15;
            $products = $this->productService->getProductsByCategories($validated['category_ids'], $perPage);
            
            return response()->json([
                'success' => true,
                'data' => $products,
                'message' => 'Products retrieved by categories successfully'
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
     * Get products without category
     */
    public function getWithoutCategory(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $products = $this->productService->getProductsWithoutCategory($perPage);
            
            return response()->json([
                'success' => true,
                'data' => $products,
                'message' => 'Products without category retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
