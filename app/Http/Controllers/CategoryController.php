<?php

namespace App\Http\Controllers;

use App\Services\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    protected CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Display a listing of categories.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $categories = $this->categoryService->getAllCategories($perPage);

            return response()->json([
                'success' => true,
                'data' => $categories,
                'message' => 'تم جلب التصنيفات بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في جلب التصنيفات: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display active categories only.
     */
    public function active(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $categories = $this->categoryService->getActiveCategories($perPage);

            return response()->json([
                'success' => true,
                'data' => $categories,
                'message' => 'تم جلب التصنيفات النشطة بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في جلب التصنيفات النشطة: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display root categories.
     */
    public function root(): JsonResponse
    {
        try {
            $categories = $this->categoryService->getRootCategories();

            return response()->json([
                'success' => true,
                'data' => $categories,
                'message' => 'تم جلب التصنيفات الرئيسية بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في جلب التصنيفات الرئيسية: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display category tree.
     */
    public function tree(): JsonResponse
    {
        try {
            $categories = $this->categoryService->getCategoryTree();

            return response()->json([
                'success' => true,
                'data' => $categories,
                'message' => 'تم جلب شجرة التصنيفات بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في جلب شجرة التصنيفات: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified category.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $category = $this->categoryService->getCategoryById($id);

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'التصنيف غير موجود'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $category,
                'message' => 'تم جلب التصنيف بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في جلب التصنيف: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display category by slug.
     */
    public function showBySlug(string $slug): JsonResponse
    {
        try {
            $category = $this->categoryService->getCategoryBySlug($slug);

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'التصنيف غير موجود'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $category,
                'message' => 'تم جلب التصنيف بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في جلب التصنيف: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'slug' => 'nullable|string|unique:categories,slug',
                'parent_id' => 'nullable|exists:categories,id',
                'is_active' => 'boolean',
                'sort_order' => 'integer|min:0'
            ]);

            $category = $this->categoryService->createCategory($validated);

            return response()->json([
                'success' => true,
                'data' => $category,
                'message' => 'تم إنشاء التصنيف بنجاح'
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في التحقق من البيانات',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في إنشاء التصنيف: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'slug' => 'sometimes|string|unique:categories,slug,' . $id,
                'parent_id' => 'nullable|exists:categories,id',
                'is_active' => 'boolean',
                'sort_order' => 'integer|min:0'
            ]);

            $category = $this->categoryService->updateCategory($id, $validated);

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'التصنيف غير موجود'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $category,
                'message' => 'تم تحديث التصنيف بنجاح'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في التحقق من البيانات',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في تحديث التصنيف: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified category.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->categoryService->deleteCategory($id);

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'التصنيف غير موجود'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'تم حذف التصنيف بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في حذف التصنيف: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search categories.
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $query = $request->get('query');
            $perPage = $request->get('per_page', 15);

            if (empty($query)) {
                return response()->json([
                    'success' => false,
                    'message' => 'يرجى إدخال كلمة البحث'
                ], 400);
            }

            $categories = $this->categoryService->searchCategories($query, $perPage);

            return response()->json([
                'success' => true,
                'data' => $categories,
                'message' => 'تم البحث في التصنيفات بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في البحث: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get category statistics.
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = $this->categoryService->getCategoryStatistics();

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'تم جلب إحصائيات التصنيفات بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في جلب الإحصائيات: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get category breadcrumb.
     */
    public function breadcrumb(int $id): JsonResponse
    {
        try {
            $breadcrumb = $this->categoryService->getCategoryBreadcrumb($id);

            return response()->json([
                'success' => true,
                'data' => $breadcrumb,
                'message' => 'تم جلب مسار التصنيف بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في جلب مسار التصنيف: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle category status.
     */
    public function toggleStatus(int $id): JsonResponse
    {
        try {
            $category = $this->categoryService->toggleCategoryStatus($id);

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'التصنيف غير موجود'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $category,
                'message' => 'تم تغيير حالة التصنيف بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في تغيير حالة التصنيف: ' . $e->getMessage()
            ], 500);
        }
    }
}
