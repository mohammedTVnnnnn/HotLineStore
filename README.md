# نظام HotLine للتجارة الإلكترونية

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
</p>

<p align="center">
  <strong>نظام تجارة إلكترونية متكامل مبني بـ Laravel Framework</strong>
</p>

---

## 📋 نظرة عامة

نظام HotLine هو نظام تجارة إلكترونية متكامل تم تطويره باستخدام Laravel Framework مع تطبيق أفضل الممارسات في البرمجة وتصميم الأنظمة. يوفر النظام إدارة شاملة للمستخدمين والمنتجات وعربات التسوق والفواتير مع لوحة تحكم متقدمة للأدمن.

### ✨ الميزات الرئيسية

- **إدارة المستخدمين**: نظام مستخدمين متقدم مع أدوار مختلفة
- **إدارة المنتجات**: إدارة شاملة للمنتجات مع البحث والفلترة
- **نظام السلة**: عربة تسوق ذكية مع إدارة تلقائية
- **نظام الفواتير**: إنشاء فواتير متكامل مع عملية Checkout
- **لوحة تحكم الأدمن**: إدارة متقدمة مع تقارير وإحصائيات
- **API متكامل**: RESTful API مع حماية شاملة
- **أمان متقدم**: مصادقة، صلاحيات، وحماية البيانات

---

## 🏗️ هيكل المشروع

### الملفات الأساسية
```
HotLine/
├── app/
│   ├── Http/Controllers/          # Controllers للـ API
│   │   ├── UserController.php
│   │   ├── ProductController.php
│   │   ├── CartController.php
│   │   ├── InvoiceController.php
│   │   └── Admin/
│   │       └── DashboardController.php
│   ├── Http/Middleware/           # Middleware للحماية
│   │   └── AdminMiddleware.php
│   ├── Models/                     # Models للبيانات
│   │   ├── User.php
│   │   ├── Product.php
│   │   ├── Cart.php
│   │   ├── CartItem.php
│   │   ├── Invoice.php
│   │   └── InvoiceItem.php
│   ├── Services/                   # Business Logic Layer
│   │   ├── UserService.php
│   │   ├── ProductService.php
│   │   ├── CartService.php
│   │   └── InvoiceService.php
│   └── Providers/
│       └── HotlineServiceProvider.php
├── routes/
│   ├── api.php                     # API Routes
│   └── admin.php                   # Admin Routes
├── database/migrations/             # Database Migrations
└── docs/                          # التوثيق
    ├── DATABASE_DOCUMENTATION.md
    ├── API_USAGE_GUIDE.md
    ├── CHECKOUT_FLOW.md
    └── SERVICE_PROVIDER_FIX.md
```

---

## 🗄️ قاعدة البيانات والجداول

### الجداول الرئيسية

#### 1. جدول المستخدمين (users)
| العمود | نوع البيانات | الوصف | القيود |
|--------|-------------|--------|--------|
| id | BIGINT UNSIGNED | المعرف الفريد للمستخدم | PRIMARY KEY, AUTO_INCREMENT |
| name | VARCHAR(255) | اسم المستخدم | NOT NULL |
| email | VARCHAR(255) | البريد الإلكتروني | NOT NULL, UNIQUE |
| password | VARCHAR(255) | كلمة المرور المشفرة | NOT NULL |
| role | VARCHAR(255) | دور المستخدم | NOT NULL |
| created_at | TIMESTAMP | تاريخ الإنشاء | NULL |
| updated_at | TIMESTAMP | تاريخ آخر تحديث | NULL |

#### 2. جدول المنتجات (products)
| العمود | نوع البيانات | الوصف | القيود |
|--------|-------------|--------|--------|
| id | BIGINT UNSIGNED | المعرف الفريد للمنتج | PRIMARY KEY, AUTO_INCREMENT |
| name | VARCHAR(255) | اسم المنتج | NOT NULL |
| description | TEXT | وصف المنتج | NULL |
| price | DECIMAL(10,2) | سعر المنتج | NOT NULL |
| stock | INTEGER | كمية المخزون | NOT NULL |
| created_at | TIMESTAMP | تاريخ الإنشاء | NULL |
| updated_at | TIMESTAMP | تاريخ آخر تحديث | NULL |

#### 3. جدول العربات (carts)
| العمود | نوع البيانات | الوصف | القيود |
|--------|-------------|--------|--------|
| id | BIGINT UNSIGNED | المعرف الفريد للعربة | PRIMARY KEY, AUTO_INCREMENT |
| user_id | BIGINT UNSIGNED | معرف المستخدم | FOREIGN KEY → users.id |
| created_at | TIMESTAMP | تاريخ الإنشاء | NULL |
| updated_at | TIMESTAMP | تاريخ آخر تحديث | NULL |

#### 4. جدول عناصر العربة (cart_items)
| العمود | نوع البيانات | الوصف | القيود |
|--------|-------------|--------|--------|
| id | BIGINT UNSIGNED | المعرف الفريد للعنصر | PRIMARY KEY, AUTO_INCREMENT |
| cart_id | BIGINT UNSIGNED | معرف العربة | FOREIGN KEY → carts.id |
| product_id | BIGINT UNSIGNED | معرف المنتج | FOREIGN KEY → products.id |
| quantity | INTEGER | الكمية المطلوبة | NOT NULL |
| created_at | TIMESTAMP | تاريخ الإنشاء | NULL |
| updated_at | TIMESTAMP | تاريخ آخر تحديث | NULL |

#### 5. جدول الفواتير (invoices)
| العمود | نوع البيانات | الوصف | القيود |
|--------|-------------|--------|--------|
| id | BIGINT UNSIGNED | المعرف الفريد للفاتورة | PRIMARY KEY, AUTO_INCREMENT |
| user_id | BIGINT UNSIGNED | معرف المستخدم | FOREIGN KEY → users.id |
| total | DECIMAL(10,2) | إجمالي المبلغ | NOT NULL |
| status | VARCHAR(255) | حالة الفاتورة | NOT NULL |
| created_at | TIMESTAMP | تاريخ الإنشاء | NULL |
| updated_at | TIMESTAMP | تاريخ آخر تحديث | NULL |

#### 6. جدول عناصر الفاتورة (invoice_items)
| العمود | نوع البيانات | الوصف | القيود |
|--------|-------------|--------|--------|
| id | BIGINT UNSIGNED | المعرف الفريد للعنصر | PRIMARY KEY, AUTO_INCREMENT |
| invoice_id | BIGINT UNSIGNED | معرف الفاتورة | FOREIGN KEY → invoices.id |
| product_id | BIGINT UNSIGNED | معرف المنتج | FOREIGN KEY → products.id |
| quantity | INTEGER | الكمية المباعة | NOT NULL |
| price | DECIMAL(10,2) | السعر وقت البيع | NOT NULL |
| created_at | TIMESTAMP | تاريخ الإنشاء | NULL |
| updated_at | TIMESTAMP | تاريخ آخر تحديث | NULL |

### العلاقات بين الجداول

```
users (1) ←→ (n) carts
carts (1) ←→ (n) cart_items
products (1) ←→ (n) cart_items
users (1) ←→ (n) invoices
invoices (1) ←→ (n) invoice_items
products (1) ←→ (n) invoice_items
```

---

## 🏛️ Models والعلاقات

### 1. User Model
**الملف**: `app/Models/User.php`

**العلاقات**:
- `hasMany(Cart::class)` - المستخدم يملك عدة عربات تسوق
- `hasMany(Invoice::class)` - المستخدم يملك عدة فواتير
- `hasOne(Cart::class)->latest()` - السلة النشطة للمستخدم

**الأعمدة القابلة للتعديل**:
```php
protected $fillable = ['name', 'email', 'password', 'role'];
```

### 2. Product Model
**الملف**: `app/Models/Product.php`

**العلاقات**:
- `hasMany(CartItem::class)` - المنتج موجود في عدة عناصر عربة
- `hasMany(InvoiceItem::class)` - المنتج موجود في عدة عناصر فاتورة

**الأعمدة القابلة للتعديل**:
```php
protected $fillable = ['name', 'description', 'price', 'stock'];
```

### 3. Cart Model
**الملف**: `app/Models/Cart.php`

**العلاقات**:
- `belongsTo(User::class)` - العربة تنتمي لمستخدم واحد
- `hasMany(CartItem::class)` - العربة تحتوي على عدة عناصر

**الأعمدة القابلة للتعديل**:
```php
protected $fillable = ['user_id'];
```

### 4. CartItem Model
**الملف**: `app/Models/CartItem.php`

**العلاقات**:
- `belongsTo(Cart::class)` - العنصر ينتمي لعربة واحدة
- `belongsTo(Product::class)` - العنصر ينتمي لمنتج واحد

**الأعمدة القابلة للتعديل**:
```php
protected $fillable = ['cart_id', 'product_id', 'quantity'];
```

### 5. Invoice Model
**الملف**: `app/Models/Invoice.php`

**العلاقات**:
- `belongsTo(User::class)` - الفاتورة تنتمي لمستخدم واحد
- `hasMany(InvoiceItem::class)` - الفاتورة تحتوي على عدة عناصر

**الأعمدة القابلة للتعديل**:
```php
protected $fillable = ['user_id', 'total', 'status'];
```

### 6. InvoiceItem Model
**الملف**: `app/Models/InvoiceItem.php`

**العلاقات**:
- `belongsTo(Invoice::class)` - العنصر ينتمي لفاتورة واحدة
- `belongsTo(Product::class)` - العنصر ينتمي لمنتج واحد

**الأعمدة القابلة للتعديل**:
```php
protected $fillable = ['invoice_id', 'product_id', 'quantity', 'price'];
```

---

## 🔐 الأدوار والصلاحيات

### أدوار المستخدمين

#### 1. Admin (مدير النظام)
- **الصلاحيات**: جميع الصلاحيات
- **العمليات المسموحة**:
  - إدارة المنتجات (إضافة، تعديل، حذف)
  - عرض جميع الفواتير
  - فلترة الفواتير
  - تقارير المبيعات
  - إدارة المستخدمين

#### 2. Manager (مدير)
- **الصلاحيات**: صلاحيات محدودة
- **العمليات المسموحة**:
  - عرض المنتجات
  - عرض الفواتير
  - تقارير أساسية

#### 3. Customer (عميل)
- **الصلاحيات**: صلاحيات العميل
- **العمليات المسموحة**:
  - عرض المنتجات
  - إدارة السلة
  - إنشاء الفواتير
  - عرض فواتيره الخاصة

### Middleware المستخدم

#### 1. auth:sanctum
- **الغرض**: التحقق من تسجيل دخول المستخدم
- **المستخدم في**: جميع العمليات المحمية

#### 2. admin
- **الغرض**: التحقق من صلاحيات admin
- **المستخدم في**: العمليات الحساسة (إدارة المنتجات، لوحة التحكم)

---

## 🚀 API Endpoints

### المستخدمين (Users)
| Method | Endpoint | الوصف | الصلاحيات |
|--------|----------|--------|-----------|
| GET | `/api/users` | عرض جميع المستخدمين | Admin |
| GET | `/api/users/{id}` | عرض مستخدم محدد | Admin |
| POST | `/api/users` | إنشاء مستخدم جديد | Admin |
| PUT | `/api/users/{id}` | تحديث مستخدم | Admin |
| DELETE | `/api/users/{id}` | حذف مستخدم | Admin |
| GET | `/api/users/{id}/relations` | عرض المستخدم مع علاقاته | Admin |
| GET | `/api/users/role/{role}` | عرض المستخدمين حسب الدور | Admin |

### المنتجات (Products)
| Method | Endpoint | الوصف | الصلاحيات |
|--------|----------|--------|-----------|
| GET | `/api/products` | عرض جميع المنتجات | Public |
| GET | `/api/products/{id}` | عرض منتج محدد | Public |
| POST | `/api/products` | إنشاء منتج جديد | Admin |
| PUT | `/api/products/{id}` | تحديث منتج | Admin |
| DELETE | `/api/products/{id}` | حذف منتج | Admin |
| GET | `/api/products/search` | البحث في المنتجات | Public |
| GET | `/api/products/price-range` | فلترة حسب السعر | Public |
| GET | `/api/products/low-stock` | المنتجات قليلة المخزون | Public |
| PUT | `/api/products/{id}/stock` | تحديث المخزون | Admin |

### السلة (Cart) - محمية بـ auth:sanctum
| Method | Endpoint | الوصف | الصلاحيات |
|--------|----------|--------|-----------|
| GET | `/api/cart` | عرض السلة الخاصة بالمستخدم | Customer |
| POST | `/api/cart` | إضافة منتج للسلة | Customer |
| PUT | `/api/cart/{id}` | تعديل كمية منتج | Customer |
| DELETE | `/api/cart/{id}` | حذف منتج من السلة | Customer |

### الفواتير (Invoices) - محمية بـ auth:sanctum
| Method | Endpoint | الوصف | الصلاحيات |
|--------|----------|--------|-----------|
| POST | `/api/checkout` | إنشاء فاتورة من السلة | Customer |
| GET | `/api/invoices` | عرض فواتير المستخدم | Customer |
| GET | `/api/invoices/{id}` | عرض تفاصيل فاتورة | Customer |

### لوحة تحكم الأدمن - محمية بـ auth:sanctum + admin
| Method | Endpoint | الوصف | الصلاحيات |
|--------|----------|--------|-----------|
| GET | `/admin/dashboard/invoices` | عرض جميع الفواتير | Admin |
| GET | `/admin/dashboard/invoices/filter` | فلترة الفواتير | Admin |
| GET | `/admin/dashboard/reports/sales` | تقارير المبيعات | Admin |

---

## 🎯 أمثلة على الاستخدام

### سيناريو كامل: عملية تسوق

#### 1. إنشاء مستخدم جديد
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

#### 2. إنشاء منتج جديد (Admin فقط)
```bash
curl -X POST http://localhost:8000/api/products \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN" \
  -d '{
    "name": "هاتف آيفون 15",
    "description": "هاتف ذكي حديث من آبل",
    "price": 4000.00,
    "stock": 25
  }'
```

#### 3. إضافة منتج للسلة
```bash
curl -X POST http://localhost:8000/api/cart \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "product_id": 1,
    "quantity": 2
  }'
```

#### 4. عرض السلة
```bash
curl -X GET http://localhost:8000/api/cart \
  -H "Authorization: Bearer YOUR_TOKEN"
```

#### 5. إنشاء فاتورة (Checkout)
```bash
curl -X POST http://localhost:8000/api/checkout \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "status": "completed"
  }'
```

#### 6. عرض فواتير المستخدم
```bash
curl -X GET http://localhost:8000/api/invoices \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## 🔧 التثبيت والتشغيل

### المتطلبات
- PHP 8.1 أو أحدث
- Composer
- MySQL/PostgreSQL
- Laravel 11

### خطوات التثبيت

#### 1. تثبيت التبعيات
```bash
composer install
```

#### 2. إعداد البيئة
```bash
cp .env.example .env
php artisan key:generate
```

#### 3. إعداد قاعدة البيانات
```bash
# تحديث ملف .env
DB_DATABASE=hotline_db
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

#### 4. تشغيل المايجريشن
```bash
php artisan migrate
```

#### 5. تشغيل الخادم
```bash
php artisan serve
```

#### 6. اختبار النظام
```bash
curl -X GET http://localhost:8000/api/products
```

---

## 🏗️ Architecture Patterns

### Service Layer Pattern
- **الغرض**: فصل منطق الأعمال عن Controllers
- **التطبيق**: جميع العمليات المعقدة في Services منفصلة

### Repository Pattern
- **الغرض**: إدارة البيانات عبر Services
- **التطبيق**: Services تتعامل مع Models مباشرة

### Dependency Injection
- **الغرض**: حقن التبعيات في Controllers
- **التطبيق**: Services مسجلة في HotlineServiceProvider

### Middleware Pattern
- **الغرض**: حماية العمليات والتحقق من الصلاحيات
- **التطبيق**: auth:sanctum و admin middleware

---

## 🔒 الأمان والحماية

### تشفير البيانات
- كلمات المرور مشفرة باستخدام Hash
- جميع البيانات الحساسة محمية

### Validation شامل
- التحقق من جميع المدخلات
- رسائل خطأ واضحة باللغة العربية

### Database Transactions
- عمليات حساسة داخل Transactions
- ضمان ACID properties

### حماية الـ API
- مصادقة باستخدام Laravel Sanctum
- صلاحيات متدرجة حسب الدور
- Middleware للحماية

---

## 📊 التقارير والإحصائيات

### تقارير المبيعات
- تقارير يومية وشهرية
- إحصائيات مفصلة للمبيعات
- أفضل العملاء والمنتجات

### فلترة متقدمة
- فلترة حسب التاريخ والمستخدم والحالة
- دعم pagination لجميع القوائم

---

## 🐛 إصلاحات خاصة

### SERVICE_PROVIDER_FIX.md
تم حل مشكلة تعارض الأسماء في ServiceProvider:
- إعادة تسمية الملف إلى `HotlineServiceProvider.php`
- تحديث تسجيل الـ Provider في `bootstrap/providers.php`
- تبسيط تسجيل الـ Services

---

## 📚 التوثيق الإضافي

### ملفات التوثيق المتاحة
- **DATABASE_DOCUMENTATION.md**: توثيق شامل لقاعدة البيانات والـ Models
- **API_USAGE_GUIDE.md**: دليل استخدام API مع أمثلة عملية
- **CHECKOUT_FLOW.md**: تدفق عملية Checkout من السلة إلى الفاتورة
- **SERVICE_PROVIDER_FIX.md**: إصلاح مشكلة ServiceProvider

---

## 🚀 الميزات المتقدمة

### نظام السلة الذكي
- إدارة تلقائية للسلة
- منع التكرار (زيادة الكمية للمنتجات الموجودة)
- التحقق من المخزون قبل الإضافة

### عملية Checkout متكاملة
- تحويل السلة إلى فاتورة تلقائياً
- إدارة المخزون التلقائية
- حفظ تاريخ الأسعار

### لوحة تحكم الأدمن
- إدارة شاملة للنظام
- تقارير وإحصائيات مفصلة
- فلترة متقدمة للبيانات

---

## 🤝 المساهمة

نرحب بالمساهمات! يرجى:
1. Fork المشروع
2. إنشاء branch جديد للميزة
3. Commit التغييرات
4. Push إلى الـ branch
5. إنشاء Pull Request

---

## 📄 الترخيص

هذا المشروع مرخص تحت [MIT License](https://opensource.org/licenses/MIT).

---

## 📞 الدعم

إذا واجهت أي مشاكل أو تحتاج مساعدة:
1. راجع ملفات التوثيق المتاحة
2. فحص logs في `storage/logs/laravel.log`
3. تأكد من تشغيل المايجريشن بشكل صحيح

---

<p align="center">
  <strong>تم تطوير هذا النظام بـ ❤️ باستخدام Laravel Framework</strong>
</p>
