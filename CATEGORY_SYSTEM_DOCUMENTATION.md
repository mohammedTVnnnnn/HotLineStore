# نظام التصنيفات - Category System Documentation

## نظرة عامة
تم إضافة نظام تصنيفات متكامل لنظام HotLine للتجارة الإلكترونية، يدعم التصنيفات الهرمية والبحث المتقدم وإدارة شاملة.

## المميزات الرئيسية

### ✅ تصنيفات هرمية
- دعم التصنيفات الرئيسية والفرعية
- إمكانية إنشاء تصنيفات متعددة المستويات
- مسار كامل للتصنيف (Breadcrumb)

### ✅ روابط ودودة (SEO-Friendly)
- دعم slugs للتصنيفات
- إنشاء تلقائي للـ slugs من الأسماء
- روابط نظيفة للبحث

### ✅ إدارة متقدمة
- ترتيب مخصص للتصنيفات
- تفعيل/إلغاء تفعيل التصنيفات
- إحصائيات شاملة

### ✅ بحث متقدم
- البحث في التصنيفات بالاسم والوصف
- فلترة المنتجات حسب التصنيف
- دعم البحث في تصنيفات متعددة

## هيكل قاعدة البيانات

### جدول التصنيفات (categories)
```sql
CREATE TABLE categories (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    parent_id BIGINT UNSIGNED NULL,
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INTEGER DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE CASCADE
);
```

### تحديث جدول المنتجات
```sql
ALTER TABLE products ADD COLUMN category_id BIGINT UNSIGNED NULL;
ALTER TABLE products ADD FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL;
```

## Models والعلاقات

### Category Model
```php
class Category extends Model
{
    protected $fillable = [
        'name', 'description', 'slug', 'parent_id', 
        'is_active', 'sort_order'
    ];

    // العلاقات
    public function products(): HasMany
    public function parent(): BelongsTo
    public function children(): HasMany
    public function descendants(): HasMany

    // Scopes
    public function scopeActive($query)
    public function scopeRoot($query)
    public function scopeOrdered($query)

    // Accessors
    public function getFullPathAttribute(): string
    public function getDepthAttribute(): int
}
```

### Product Model (محدث)
```php
class Product extends Model
{
    protected $fillable = [
        'name', 'description', 'price', 'stock', 
        'image', 'category_id'
    ];

    // العلاقة الجديدة
    public function category(): BelongsTo
}
```

## Services

### CategoryService
```php
class CategoryService
{
    // العمليات الأساسية
    public function getAllCategories(int $perPage = 15): LengthAwarePaginator
    public function getActiveCategories(int $perPage = 15): LengthAwarePaginator
    public function getRootCategories(): Collection
    public function getCategoryTree(): Collection
    public function getCategoryById(int $id): ?Category
    public function getCategoryBySlug(string $slug): ?Category

    // العمليات المتقدمة
    public function createCategory(array $data): Category
    public function updateCategory(int $id, array $data): ?Category
    public function deleteCategory(int $id): bool
    public function searchCategories(string $query, int $perPage = 15): LengthAwarePaginator
    public function getCategoryStatistics(): array
    public function toggleCategoryStatus(int $id): ?Category
    public function getCategoryBreadcrumb(int $id): array
}
```

### ProductService (محدث)
```php
class ProductService
{
    // دوال التصنيفات الجديدة
    public function getProductsByCategory(int $categoryId, int $perPage = 15): LengthAwarePaginator
    public function getProductsByCategorySlug(string $slug, int $perPage = 15): LengthAwarePaginator
    public function getProductsWithCategories(int $perPage = 15): LengthAwarePaginator
    public function searchProductsByCategory(string $query, int $categoryId, int $perPage = 15): LengthAwarePaginator
    public function getProductsByCategories(array $categoryIds, int $perPage = 15): LengthAwarePaginator
    public function getProductsWithoutCategory(int $perPage = 15): LengthAwarePaginator
}
```

## Controllers

### CategoryController
```php
class CategoryController extends Controller
{
    // العمليات العامة
    public function index(Request $request): JsonResponse
    public function active(Request $request): JsonResponse
    public function root(): JsonResponse
    public function tree(): JsonResponse
    public function show(int $id): JsonResponse
    public function showBySlug(string $slug): JsonResponse
    public function search(Request $request): JsonResponse
    public function statistics(): JsonResponse
    public function breadcrumb(int $id): JsonResponse

    // العمليات المحمية (Admin فقط)
    public function store(Request $request): JsonResponse
    public function update(Request $request, int $id): JsonResponse
    public function destroy(int $id): JsonResponse
    public function toggleStatus(int $id): JsonResponse
}
```

### ProductController (محدث)
```php
class ProductController extends Controller
{
    // دوال التصنيفات الجديدة
    public function getByCategory(Request $request, int $categoryId): JsonResponse
    public function getByCategorySlug(Request $request, string $slug): JsonResponse
    public function getWithCategories(Request $request): JsonResponse
    public function searchByCategory(Request $request, int $categoryId): JsonResponse
    public function getByCategories(Request $request): JsonResponse
    public function getWithoutCategory(Request $request): JsonResponse
}
```

## API Endpoints

### تصنيفات (Categories)
| Method | Endpoint | الوصف | الصلاحيات |
|--------|----------|--------|-----------|
| GET | `/api/categories` | عرض جميع التصنيفات | Public |
| GET | `/api/categories/active` | عرض التصنيفات النشطة | Public |
| GET | `/api/categories/root` | عرض التصنيفات الرئيسية | Public |
| GET | `/api/categories/tree` | عرض شجرة التصنيفات | Public |
| GET | `/api/categories/search` | البحث في التصنيفات | Public |
| GET | `/api/categories/statistics` | إحصائيات التصنيفات | Public |
| GET | `/api/categories/{id}` | عرض تصنيف محدد | Public |
| GET | `/api/categories/slug/{slug}` | عرض تصنيف بالرابط | Public |
| GET | `/api/categories/{id}/breadcrumb` | مسار التصنيف | Public |
| POST | `/api/categories` | إنشاء تصنيف جديد | Admin |
| PUT | `/api/categories/{id}` | تحديث تصنيف | Admin |
| DELETE | `/api/categories/{id}` | حذف تصنيف | Admin |
| PUT | `/api/categories/{id}/toggle-status` | تغيير حالة التصنيف | Admin |

### منتجات (Products) - محدثة
| Method | Endpoint | الوصف | الصلاحيات |
|--------|----------|--------|-----------|
| GET | `/api/products/with-categories` | المنتجات مع التصنيفات | Public |
| GET | `/api/products/without-category` | المنتجات بدون تصنيف | Public |
| GET | `/api/products/category/{categoryId}` | المنتجات حسب التصنيف | Public |
| GET | `/api/products/category-slug/{slug}` | المنتجات حسب رابط التصنيف | Public |
| GET | `/api/products/categories` | المنتجات حسب تصنيفات متعددة | Public |
| GET | `/api/products/category/{categoryId}/search` | البحث في تصنيف محدد | Public |

## أمثلة على الاستخدام

### إنشاء تصنيف جديد
```bash
curl -X POST http://localhost:8000/api/categories \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN" \
  -d '{
    "name": "إلكترونيات",
    "description": "جميع الأجهزة الإلكترونية",
    "slug": "electronics",
    "is_active": true,
    "sort_order": 1
  }'
```

### إنشاء تصنيف فرعي
```bash
curl -X POST http://localhost:8000/api/categories \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN" \
  -d '{
    "name": "هواتف ذكية",
    "description": "هواتف ذكية حديثة",
    "slug": "smartphones",
    "parent_id": 1,
    "is_active": true,
    "sort_order": 1
  }'
```

### إنشاء منتج مع تصنيف
```bash
curl -X POST http://localhost:8000/api/products \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN" \
  -d '{
    "name": "هاتف آيفون 15",
    "description": "هاتف ذكي حديث من آبل",
    "price": 4000.00,
    "stock": 25,
    "category_id": 1
  }'
```

### البحث في منتجات تصنيف محدد
```bash
curl -X GET "http://localhost:8000/api/products/category/1/search?query=آيفون" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### الحصول على شجرة التصنيفات
```bash
curl -X GET http://localhost:8000/api/categories/tree
```

### إحصائيات التصنيفات
```bash
curl -X GET http://localhost:8000/api/categories/statistics
```

## الاستجابات المتوقعة

### تصنيف مع المنتجات
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "إلكترونيات",
        "description": "جميع الأجهزة الإلكترونية",
        "slug": "electronics",
        "parent_id": null,
        "is_active": true,
        "sort_order": 1,
        "full_path": "إلكترونيات",
        "depth": 0,
        "products": [
            {
                "id": 1,
                "name": "هاتف آيفون 15",
                "price": "4000.00",
                "stock": 25
            }
        ],
        "created_at": "2024-01-01T10:00:00.000000Z",
        "updated_at": "2024-01-01T10:00:00.000000Z"
    },
    "message": "تم جلب التصنيف بنجاح"
}
```

### منتج مع تصنيفه
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "هاتف آيفون 15",
        "description": "هاتف ذكي حديث من آبل",
        "price": "4000.00",
        "stock": 25,
        "category_id": 1,
        "category": {
            "id": 1,
            "name": "هواتف ذكية",
            "slug": "smartphones",
            "full_path": "إلكترونيات > هواتف ذكية"
        },
        "image": "products/iphone15.jpg",
        "image_url": "http://localhost:8000/storage/products/iphone15.jpg",
        "created_at": "2024-01-15T10:30:00.000000Z",
        "updated_at": "2024-01-15T10:30:00.000000Z"
    },
    "message": "تم جلب المنتج بنجاح"
}
```

## الميزات المتقدمة

### 1. التصنيفات الهرمية
- دعم تصنيفات متعددة المستويات
- مسار كامل للتصنيف (Breadcrumb)
- حساب عمق التصنيف تلقائياً

### 2. الروابط الودودة
- إنشاء تلقائي للـ slugs
- دعم الروابط العربية والإنجليزية
- فهرسة محسنة للبحث

### 3. إدارة متقدمة
- ترتيب مخصص للتصنيفات
- تفعيل/إلغاء تفعيل التصنيفات
- إحصائيات شاملة

### 4. البحث والفلترة
- البحث في التصنيفات بالاسم والوصف
- فلترة المنتجات حسب التصنيف
- دعم البحث في تصنيفات متعددة

### 5. الأمان
- حماية العمليات الحساسة بـ Admin middleware
- التحقق من صحة البيانات
- معالجة الأخطاء الشاملة

## ملاحظات مهمة

1. **الترتيب**: التصنيفات مرتبة حسب `sort_order` ثم `name`
2. **الحذف**: عند حذف تصنيف، المنتجات تنتقل للتصنيف الأب أو تصبح بدون تصنيف
3. **الروابط**: الـ slugs يتم إنشاؤها تلقائياً من الأسماء
4. **الأداء**: تم إضافة فهارس لتحسين الأداء
5. **العلاقات**: جميع العلاقات محمية بـ CASCADE أو SET NULL
6. **التحقق**: تم تطبيق validation شامل لجميع العمليات
7. **الاستجابات**: جميع الاستجابات تتبع نفس التنسيق الموحد

## التطوير المستقبلي

- [ ] دعم الصور للتصنيفات
- [ ] ترجمة التصنيفات للغات متعددة
- [ ] تصدير/استيراد التصنيفات
- [ ] إحصائيات متقدمة للتصنيفات
- [ ] دعم التصنيفات الديناميكية
- [ ] تكامل مع أنظمة SEO متقدمة
