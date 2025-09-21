<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CategoryService
{
    /**
     * Get all categories with pagination.
     */
    public function getAllCategories(int $perPage = 15): LengthAwarePaginator
    {
        return Category::with(['parent', 'children'])
            ->ordered()
            ->paginate($perPage);
    }

    /**
     * Get active categories only.
     */
    public function getActiveCategories(int $perPage = 15): LengthAwarePaginator
    {
        return Category::active()
            ->with(['parent', 'children'])
            ->ordered()
            ->paginate($perPage);
    }

    /**
     * Get root categories (no parent).
     */
    public function getRootCategories(): Collection
    {
        return Category::root()
            ->active()
            ->with('children')
            ->ordered()
            ->get();
    }

    /**
     * Get category by ID.
     */
    public function getCategoryById(int $id): ?Category
    {
        return Category::with(['parent', 'children', 'products'])
            ->find($id);
    }

    /**
     * Get category by slug.
     */
    public function getCategoryBySlug(string $slug): ?Category
    {
        return Category::with(['parent', 'children', 'products'])
            ->where('slug', $slug)
            ->first();
    }

    /**
     * Get categories by parent ID.
     */
    public function getCategoriesByParent(int $parentId): Collection
    {
        return Category::where('parent_id', $parentId)
            ->active()
            ->with('children')
            ->ordered()
            ->get();
    }

    /**
     * Get category tree (hierarchical structure).
     */
    public function getCategoryTree(): Collection
    {
        return Category::root()
            ->active()
            ->with(['children' => function ($query) {
                $query->active()->with('children');
            }])
            ->ordered()
            ->get();
    }

    /**
     * Create a new category.
     */
    public function createCategory(array $data): Category
    {
        return DB::transaction(function () use ($data) {
            return Category::create($data);
        });
    }

    /**
     * Update category.
     */
    public function updateCategory(int $id, array $data): ?Category
    {
        $category = Category::find($id);
        
        if (!$category) {
            return null;
        }

        return DB::transaction(function () use ($category, $data) {
            $category->update($data);
            return $category->fresh();
        });
    }

    /**
     * Delete category.
     */
    public function deleteCategory(int $id): bool
    {
        $category = Category::find($id);
        
        if (!$category) {
            return false;
        }

        return DB::transaction(function () use ($category) {
            // Move products to parent category or null
            if ($category->parent_id) {
                $category->products()->update(['category_id' => $category->parent_id]);
            } else {
                $category->products()->update(['category_id' => null]);
            }

            // Move children to parent's parent or null
            $newParentId = $category->parent_id;
            $category->children()->update(['parent_id' => $newParentId]);

            return $category->delete();
        });
    }

    /**
     * Get categories with product count.
     */
    public function getCategoriesWithProductCount(): Collection
    {
        return Category::active()
            ->withCount('products')
            ->with('parent')
            ->ordered()
            ->get();
    }

    /**
     * Search categories by name.
     */
    public function searchCategories(string $query, int $perPage = 15): LengthAwarePaginator
    {
        return Category::where('name', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->with(['parent', 'children'])
            ->ordered()
            ->paginate($perPage);
    }

    /**
     * Get category statistics.
     */
    public function getCategoryStatistics(): array
    {
        return [
            'total_categories' => Category::count(),
            'active_categories' => Category::active()->count(),
            'root_categories' => Category::root()->active()->count(),
            'categories_with_products' => Category::has('products')->count(),
            'categories_without_products' => Category::doesntHave('products')->count(),
        ];
    }

    /**
     * Reorder categories.
     */
    public function reorderCategories(array $categoryOrders): bool
    {
        return DB::transaction(function () use ($categoryOrders) {
            foreach ($categoryOrders as $order) {
                Category::where('id', $order['id'])
                    ->update(['sort_order' => $order['sort_order']]);
            }
            return true;
        });
    }

    /**
     * Toggle category status.
     */
    public function toggleCategoryStatus(int $id): ?Category
    {
        $category = Category::find($id);
        
        if (!$category) {
            return null;
        }

        $category->update(['is_active' => !$category->is_active]);
        
        return $category->fresh();
    }

    /**
     * Get category breadcrumb.
     */
    public function getCategoryBreadcrumb(int $id): array
    {
        $category = Category::with('parent')->find($id);
        
        if (!$category) {
            return [];
        }

        $breadcrumb = [];
        $current = $category;

        while ($current) {
            array_unshift($breadcrumb, [
                'id' => $current->id,
                'name' => $current->name,
                'slug' => $current->slug,
            ]);
            $current = $current->parent;
        }

        return $breadcrumb;
    }
}
