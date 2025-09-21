# HotLine - نظام التجارة الإلكترونية

نظام تجارة إلكترونية متكامل مبني باستخدام Laravel Framework لإدارة المبيعات بالجملة.

## نظرة عامة

HotLine هو نظام تجارة إلكترونية شامل يدعم:
- إدارة المستخدمين والأدوار
- إدارة المنتجات والمخزون
- عربات التسوق التفاعلية
- نظام الفواتير المتقدم
- تقارير المبيعات والإحصائيات

## المميزات الرئيسية

### 🛍️ إدارة المنتجات
- إضافة وتعديل وحذف المنتجات
- رفع وإدارة صور المنتجات (للأدمن فقط)
- إدارة المخزون تلقائياً
- البحث والفلترة المتقدمة
- تنبيهات المخزون المنخفض
- نظام تصنيفات هرمية متكامل
- فلترة المنتجات حسب التصنيف
- دعم الروابط الودودة للتصنيفات

### 🛒 عربات التسوق
- عربات متعددة لكل مستخدم
- إضافة وحذف المنتجات بسهولة
- حساب الإجمالي التلقائي
- التحقق من صحة العربة قبل الدفع

### 📄 نظام الفواتير
- إنشاء فواتير من العربات
- إنشاء فواتير يدوياً
- تتبع حالات الفواتير
- تحديث المخزون تلقائياً

### 👥 إدارة المستخدمين
- نظام أدوار متقدم (Admin, Customer, Manager)
- تشفير كلمات المرور
- إدارة شاملة للمستخدمين

### 📂 نظام التصنيفات
- تصنيفات هرمية متعددة المستويات
- روابط ودودة للـ SEO
- ترتيب مخصص للتصنيفات
- تفعيل/إلغاء تفعيل التصنيفات
- بحث متقدم في التصنيفات
- إحصائيات شاملة للتصنيفات

### 📊 التقارير والإحصائيات
- إحصائيات المبيعات الشاملة
- تقارير حسب الفترة الزمنية
- تحليل أداء المنتجات
- تقارير المستخدمين

## التقنيات المستخدمة

- **Laravel 12** - Framework PHP متقدم
- **MySQL** - قاعدة البيانات
- **Eloquent ORM** - إدارة قاعدة البيانات
- **RESTful API** - واجهة برمجية متكاملة
- **Service Layer Pattern** - فصل منطق الأعمال
- **Dependency Injection** - حقن التبعيات

## التثبيت والتشغيل

### المتطلبات
- PHP 8.2 أو أحدث
- Composer
- MySQL 5.7 أو أحدث
- Laravel CLI

### خطوات التثبيت

1. **استنساخ المشروع**
```bash
git clone <repository-url>
cd HotLine
```

2. **تثبيت التبعيات**
```bash
composer install
```

3. **إعداد البيئة**
```bash
cp .env.example .env
php artisan key:generate
```

4. **إعداد قاعدة البيانات**
```bash
# تحديث ملف .env بقاعدة البيانات
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hotline_db
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. **تشغيل المايجريشن**
```bash
php artisan migrate
```

6. **تشغيل الخادم**
```bash
php artisan serve
```

## هيكل المشروع

```
HotLine/
├── app/
│   ├── Http/Controllers/     # Controllers
│   │   ├── UserController.php
│   │   ├── ProductController.php
│   │   ├── CartController.php
│   │   └── InvoiceController.php
│   ├── Models/               # Eloquent Models
│   │   ├── User.php
│   │   ├── Product.php
│   │   ├── Cart.php
│   │   ├── CartItem.php
│   │   ├── Invoice.php
│   │   └── InvoiceItem.php
│   ├── Services/            # Business Logic Layer
│   │   ├── UserService.php
│   │   ├── ProductService.php
│   │   ├── CartService.php
│   │   └── InvoiceService.php
│   └── Providers/            # Service Providers
│       └── HotlineServiceProvider.php
├── database/
│   └── migrations/           # Database Migrations
├── routes/
│   ├── web.php              # Web Routes
│   └── api.php              # API Routes
├── DATABASE_DOCUMENTATION.md # توثيق قاعدة البيانات
└── API_USAGE_GUIDE.md       # دليل استخدام API
```

## استخدام API

### Base URL
```
http://localhost:8000/api
```

### أمثلة سريعة

#### إنشاء مستخدم جديد
```bash
curl -X POST http://localhost:8000/api/users \
  -H "Content-Type: application/json" \
  -d '{
    "name": "أحمد محمد",
    "email": "ahmed@example.com",
    "password": "password123",
    "role": "customer"
  }'
```

#### إنشاء منتج جديد
```bash
# إنشاء منتج بدون صورة
curl -X POST http://localhost:8000/api/products \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN" \
  -d '{
    "name": "لابتوب ديل",
    "description": "لابتوب عالي الأداء",
    "price": 2500.00,
    "stock": 10
  }'

# إنشاء منتج مع صورة
curl -X POST http://localhost:8000/api/products \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN" \
  -F "name=لابتوب ديل" \
  -F "description=لابتوب عالي الأداء" \
  -F "price=2500.00" \
  -F "stock=10" \
  -F "image=@/path/to/image.jpg"
```

#### إضافة منتج للعربة
```bash
curl -X POST http://localhost:8000/api/carts/1/add-product \
  -H "Content-Type: application/json" \
  -d '{
    "product_id": 1,
    "quantity": 2
  }'
```

#### إنشاء فاتورة من عربة
```bash
curl -X POST http://localhost:8000/api/invoices/from-cart \
  -H "Content-Type: application/json" \
  -d '{
    "cart_id": 1,
    "status": "completed"
  }'
```

#### إنشاء تصنيف جديد
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

#### إنشاء منتج مع تصنيف
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

#### البحث في منتجات تصنيف محدد
```bash
curl -X GET "http://localhost:8000/api/products/category/1/search?query=آيفون"
```

#### الحصول على شجرة التصنيفات
```bash
curl -X GET http://localhost:8000/api/categories/tree
```

## التوثيق

- **[DATABASE_DOCUMENTATION.md](DATABASE_DOCUMENTATION.md)** - توثيق شامل لقاعدة البيانات والـ Models
- **[API_USAGE_GUIDE.md](API_USAGE_GUIDE.md)** - دليل استخدام API مع أمثلة عملية
- **[CATEGORY_SYSTEM_DOCUMENTATION.md](CATEGORY_SYSTEM_DOCUMENTATION.md)** - توثيق شامل لنظام التصنيفات

## دعم التصنيفات في المنتجات

### المميزات:
- **تصنيفات هرمية**: دعم التصنيفات الرئيسية والفرعية
- **روابط ودودة**: دعم slugs للـ SEO
- **ترتيب مخصص**: إمكانية ترتيب التصنيفات حسب `sort_order`
- **تفعيل/إلغاء تفعيل**: إمكانية إخفاء التصنيفات غير المرغوبة
- **البحث المتقدم**: البحث في التصنيفات بالاسم والوصف
- **الإحصائيات**: إحصائيات شاملة للتصنيفات وعدد المنتجات

### مثال على الاستجابة مع التصنيف:
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
    "message": "Product created successfully"
}
```

## دعم الصور في المنتجات

### المميزات:
- **رفع الصور**: دعم رفع صور للمنتجات (للأدمن فقط)
- **أنواع مدعومة**: JPEG, PNG, JPG, GIF, SVG
- **حجم أقصى**: 2 ميجابايت
- **التخزين**: الصور محفوظة في `storage/app/public/products/`
- **الوصول**: الصور متاحة عبر رابط `asset('storage/products/filename')`

### مثال على الاستجابة:
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "لابتوب ديل",
        "description": "لابتوب عالي الأداء",
        "price": "2500.00",
        "stock": 10,
        "image": "products/abc123.jpg",
        "image_url": "http://localhost:8000/storage/products/abc123.jpg",
        "created_at": "2024-01-15T10:30:00.000000Z",
        "updated_at": "2024-01-15T10:30:00.000000Z"
    },
    "message": "Product created successfully"
}
```

## الـ Endpoints الرئيسية

### المستخدمين
- `GET /api/users` - عرض جميع المستخدمين
- `POST /api/users` - إنشاء مستخدم جديد
- `GET /api/users/{id}` - عرض مستخدم محدد
- `PUT /api/users/{id}` - تحديث مستخدم
- `DELETE /api/users/{id}` - حذف مستخدم

### المنتجات
- `GET /api/products` - عرض جميع المنتجات
- `POST /api/products` - إنشاء منتج جديد
- `GET /api/products/search` - البحث في المنتجات
- `GET /api/products/low-stock` - المنتجات قليلة المخزون

### العربات
- `POST /api/carts` - إنشاء عربة جديدة
- `POST /api/carts/{id}/add-product` - إضافة منتج للعربة
- `GET /api/carts/{id}/total` - حساب إجمالي العربة
- `GET /api/carts/{id}/validate-checkout` - التحقق من صحة العربة

### الفواتير
- `POST /api/invoices/from-cart` - إنشاء فاتورة من عربة
- `GET /api/invoices/sales-statistics` - إحصائيات المبيعات
- `GET /api/invoices/user/{id}` - فواتير المستخدم

### التصنيفات
- `GET /api/categories` - عرض جميع التصنيفات
- `GET /api/categories/active` - عرض التصنيفات النشطة
- `GET /api/categories/root` - عرض التصنيفات الرئيسية
- `GET /api/categories/tree` - عرض شجرة التصنيفات
- `GET /api/categories/search` - البحث في التصنيفات
- `GET /api/categories/statistics` - إحصائيات التصنيفات
- `POST /api/categories` - إنشاء تصنيف جديد (Admin)
- `PUT /api/categories/{id}` - تحديث تصنيف (Admin)
- `DELETE /api/categories/{id}` - حذف تصنيف (Admin)

## الأمان

- تشفير كلمات المرور باستخدام Laravel Hash
- Validation شامل لجميع المدخلات
- Database Transactions للعمليات الحساسة
- Error Handling متقدم

## الأداء

- Pagination لجميع القوائم
- Eager Loading للعلاقات
- Database Indexing محسن
- Service Layer Pattern لفصل المنطق

## الاختبار

```bash
# تشغيل الاختبارات
php artisan test

# تشغيل الاختبارات مع التغطية
php artisan test --coverage
```

## المساهمة

نرحب بمساهماتكم! يرجى:
1. Fork المشروع
2. إنشاء branch جديد للميزة
3. Commit التغييرات
4. Push إلى الـ branch
5. إنشاء Pull Request

## الترخيص

هذا المشروع مرخص تحت رخصة MIT. راجع ملف [LICENSE](LICENSE) للتفاصيل.

## الدعم

إذا واجهت أي مشاكل أو تحتاج مساعدة:
- راجع ملف التوثيق
- تحقق من الـ logs في `storage/logs/`
- تأكد من إعداد قاعدة البيانات بشكل صحيح

## التطوير المستقبلي

- [ ] نظام المصادقة والتفويض
- [ ] واجهة مستخدم ويب
- [ ] نظام الإشعارات
- [ ] تقارير متقدمة
- [ ] نظام الخصومات
- [ ] دعم العملات المتعددة
- [ ] نظام التقييمات والمراجعات
