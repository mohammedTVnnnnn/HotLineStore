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
- إدارة المخزون تلقائياً
- البحث والفلترة المتقدمة
- تنبيهات المخزون المنخفض

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
curl -X POST http://localhost:8000/api/products \
  -H "Content-Type: application/json" \
  -d '{
    "name": "لابتوب ديل",
    "description": "لابتوب عالي الأداء",
    "price": 2500.00,
    "stock": 10
  }'
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

## التوثيق

- **[DATABASE_DOCUMENTATION.md](DATABASE_DOCUMENTATION.md)** - توثيق شامل لقاعدة البيانات والـ Models
- **[API_USAGE_GUIDE.md](API_USAGE_GUIDE.md)** - دليل استخدام API مع أمثلة عملية

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
