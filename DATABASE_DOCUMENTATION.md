# Database Documentation - HotLine E-commerce System

## نظرة عامة
هذا التوثيق يوضح هيكل قاعدة البيانات لنظام التجارة الإلكترونية HotLine، والذي يتضمن جداول المستخدمين والمنتجات وعربات التسوق والفواتير.

## الجداول والعلاقات

### 1. جدول المستخدمين (users)
**الغرض**: تخزين معلومات المستخدمين في النظام

| العمود | نوع البيانات | الوصف | القيود |
|--------|-------------|--------|--------|
| id | BIGINT UNSIGNED | المعرف الفريد للمستخدم | PRIMARY KEY, AUTO_INCREMENT |
| name | VARCHAR(255) | اسم المستخدم | NOT NULL |
| email | VARCHAR(255) | البريد الإلكتروني | NOT NULL, UNIQUE |
| password | VARCHAR(255) | كلمة المرور المشفرة | NOT NULL |
| role | VARCHAR(255) | دور المستخدم في النظام | NOT NULL |
| created_at | TIMESTAMP | تاريخ الإنشاء | NULL |
| updated_at | TIMESTAMP | تاريخ آخر تحديث | NULL |

### 2. جدول المنتجات (products)
**الغرض**: تخزين معلومات المنتجات المتاحة للبيع

| العمود | نوع البيانات | الوصف | القيود |
|--------|-------------|--------|--------|
| id | BIGINT UNSIGNED | المعرف الفريد للمنتج | PRIMARY KEY, AUTO_INCREMENT |
| name | VARCHAR(255) | اسم المنتج | NOT NULL |
| description | TEXT | وصف المنتج | NULL |
| price | DECIMAL(10,2) | سعر المنتج | NOT NULL |
| stock | INTEGER | كمية المخزون | NOT NULL |
| image | VARCHAR(255) | مسار الصورة المخزنة للمنتج | NULL |
| created_at | TIMESTAMP | تاريخ الإنشاء | NULL |
| updated_at | TIMESTAMP | تاريخ آخر تحديث | NULL |

### 3. جدول العربات (carts)
**الغرض**: تخزين عربات التسوق للمستخدمين

| العمود | نوع البيانات | الوصف | القيود |
|--------|-------------|--------|--------|
| id | BIGINT UNSIGNED | المعرف الفريد للعربة | PRIMARY KEY, AUTO_INCREMENT |
| user_id | BIGINT UNSIGNED | معرف المستخدم | FOREIGN KEY → users.id |
| created_at | TIMESTAMP | تاريخ الإنشاء | NULL |
| updated_at | TIMESTAMP | تاريخ آخر تحديث | NULL |

### 4. جدول عناصر العربة (cart_items)
**الغرض**: تخزين المنتجات الموجودة في عربة التسوق

| العمود | نوع البيانات | الوصف | القيود |
|--------|-------------|--------|--------|
| id | BIGINT UNSIGNED | المعرف الفريد للعنصر | PRIMARY KEY, AUTO_INCREMENT |
| cart_id | BIGINT UNSIGNED | معرف العربة | FOREIGN KEY → carts.id |
| product_id | BIGINT UNSIGNED | معرف المنتج | FOREIGN KEY → products.id |
| quantity | INTEGER | الكمية المطلوبة | NOT NULL |
| created_at | TIMESTAMP | تاريخ الإنشاء | NULL |
| updated_at | TIMESTAMP | تاريخ آخر تحديث | NULL |

### 5. جدول الفواتير (invoices)
**الغرض**: تخزين فواتير المبيعات

| العمود | نوع البيانات | الوصف | القيود |
|--------|-------------|--------|--------|
| id | BIGINT UNSIGNED | المعرف الفريد للفاتورة | PRIMARY KEY, AUTO_INCREMENT |
| user_id | BIGINT UNSIGNED | معرف المستخدم | FOREIGN KEY → users.id |
| total | DECIMAL(10,2) | إجمالي المبلغ | NOT NULL |
| status | VARCHAR(255) | حالة الفاتورة | NOT NULL |
| created_at | TIMESTAMP | تاريخ الإنشاء | NULL |
| updated_at | TIMESTAMP | تاريخ آخر تحديث | NULL |

### 6. جدول عناصر الفاتورة (invoice_items)
**الغرض**: تخزين تفاصيل المنتجات في كل فاتورة

| العمود | نوع البيانات | الوصف | القيود |
|--------|-------------|--------|--------|
| id | BIGINT UNSIGNED | المعرف الفريد للعنصر | PRIMARY KEY, AUTO_INCREMENT |
| invoice_id | BIGINT UNSIGNED | معرف الفاتورة | FOREIGN KEY → invoices.id |
| product_id | BIGINT UNSIGNED | معرف المنتج | FOREIGN KEY → products.id |
| quantity | INTEGER | الكمية المباعة | NOT NULL |
| price | DECIMAL(10,2) | السعر وقت البيع | NOT NULL |
| created_at | TIMESTAMP | تاريخ الإنشاء | NULL |
| updated_at | TIMESTAMP | تاريخ آخر تحديث | NULL |

## العلاقات بين الجداول

### العلاقات الرئيسية:

1. **users → carts** (One-to-Many)
   - كل مستخدم يمكن أن يملك عدة عربات تسوق
   - العلاقة: `users.id` → `carts.user_id`

2. **carts → cart_items** (One-to-Many)
   - كل عربة تحتوي على عدة عناصر
   - العلاقة: `carts.id` → `cart_items.cart_id`

3. **products → cart_items** (One-to-Many)
   - كل منتج يمكن أن يكون في عدة عربات
   - العلاقة: `products.id` → `cart_items.product_id`

4. **users → invoices** (One-to-Many)
   - كل مستخدم يمكن أن يملك عدة فواتير
   - العلاقة: `users.id` → `invoices.user_id`

5. **invoices → invoice_items** (One-to-Many)
   - كل فاتورة تحتوي على عدة عناصر
   - العلاقة: `invoices.id` → `invoice_items.invoice_id`

6. **products → invoice_items** (One-to-Many)
   - كل منتج يمكن أن يكون في عدة فواتير
   - العلاقة: `products.id` → `invoice_items.product_id`

## قواعد الحذف (Cascade Rules)

- عند حذف مستخدم، يتم حذف جميع عرباته وفواتيره تلقائياً
- عند حذف عربة، يتم حذف جميع عناصرها تلقائياً
- عند حذف فاتورة، يتم حذف جميع عناصرها تلقائياً
- عند حذف منتج، يتم حذف جميع مراجعه في العربات والفواتير تلقائياً

## كيفية تشغيل Migrations

لتطبيق هذه الهيكلة على قاعدة البيانات، قم بتشغيل الأمر التالي:

```bash
php artisan migrate
```

## Laravel Models والعلاقات

### 1. User Model
**الملف**: `app/Models/User.php`

**العلاقات**:
- `hasMany(Cart::class)` - المستخدم يملك عدة عربات تسوق
- `hasMany(Invoice::class)` - المستخدم يملك عدة فواتير

**الأعمدة القابلة للتعديل**:
```php
protected $fillable = ['name', 'email', 'password', 'role'];
```

**أمثلة على الاستخدام**:
```php
// الحصول على جميع عربات المستخدم
$user = User::find(1);
$carts = $user->carts;

// الحصول على جميع فواتير المستخدم
$invoices = $user->invoices;

// إنشاء عربة جديدة للمستخدم
$cart = $user->carts()->create(['user_id' => $user->id]);
```

### 2. Product Model
**الملف**: `app/Models/Product.php`

**العلاقات**:
- `hasMany(CartItem::class)` - المنتج موجود في عدة عناصر عربة
- `hasMany(InvoiceItem::class)` - المنتج موجود في عدة عناصر فاتورة

**الأعمدة القابلة للتعديل**:
```php
protected $fillable = ['name', 'description', 'price', 'stock', 'image'];
```

**أمثلة على الاستخدام**:
```php
// الحصول على جميع عناصر العربة التي تحتوي على هذا المنتج
$product = Product::find(1);
$cartItems = $product->cartItems;

// الحصول على جميع عناصر الفاتورة التي تحتوي على هذا المنتج
$invoiceItems = $product->invoiceItems;

// إنشاء منتج جديد
$product = Product::create([
    'name' => 'منتج جديد',
    'description' => 'وصف المنتج',
    'price' => 100.50,
    'stock' => 10,
    'image' => 'products/abc123.jpg' // مسار الصورة المخزنة
]);

// الحصول على رابط الصورة
$imageUrl = $product->image_url; // يعيد: http://localhost:8000/storage/products/abc123.jpg
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

**أمثلة على الاستخدام**:
```php
// الحصول على المستخدم صاحب العربة
$cart = Cart::find(1);
$user = $cart->user;

// الحصول على جميع عناصر العربة
$cartItems = $cart->cartItems;

// إنشاء عنصر جديد في العربة
$cartItem = $cart->cartItems()->create([
    'product_id' => 1,
    'quantity' => 2
]);
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

**أمثلة على الاستخدام**:
```php
// الحصول على العربة التي ينتمي إليها العنصر
$cartItem = CartItem::find(1);
$cart = $cartItem->cart;

// الحصول على المنتج الذي ينتمي إليه العنصر
$product = $cartItem->product;

// تحديث كمية العنصر
$cartItem->update(['quantity' => 5]);
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

**أمثلة على الاستخدام**:
```php
// الحصول على المستخدم صاحب الفاتورة
$invoice = Invoice::find(1);
$user = $invoice->user;

// الحصول على جميع عناصر الفاتورة
$invoiceItems = $invoice->invoiceItems;

// إنشاء فاتورة جديدة
$invoice = Invoice::create([
    'user_id' => 1,
    'total' => 250.75,
    'status' => 'pending'
]);
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

**أمثلة على الاستخدام**:
```php
// الحصول على الفاتورة التي ينتمي إليها العنصر
$invoiceItem = InvoiceItem::find(1);
$invoice = $invoiceItem->invoice;

// الحصول على المنتج الذي ينتمي إليه العنصر
$product = $invoiceItem->product;

// إنشاء عنصر جديد في الفاتورة
$invoiceItem = InvoiceItem::create([
    'invoice_id' => 1,
    'product_id' => 2,
    'quantity' => 3,
    'price' => 50.00
]);
```

## أمثلة متقدمة على الاستخدام

### الحصول على جميع المنتجات في عربة المستخدم:
```php
$user = User::find(1);
$products = $user->carts()
    ->with('cartItems.product')
    ->get()
    ->pluck('cartItems')
    ->flatten()
    ->pluck('product');
```

### حساب إجمالي عربة المستخدم:
```php
$user = User::find(1);
$cartTotal = $user->carts()
    ->with('cartItems.product')
    ->get()
    ->pluck('cartItems')
    ->flatten()
    ->sum(function ($item) {
        return $item->quantity * $item->product->price;
    });
```

### الحصول على تاريخ مشتريات المستخدم:
```php
$user = User::find(1);
$purchaseHistory = $user->invoices()
    ->with('invoiceItems.product')
    ->orderBy('created_at', 'desc')
    ->get();
```

## Controllers والـ API Endpoints

### 1. UserController
**الملف**: `app/Http/Controllers/UserController.php`

**الـ Endpoints**:
- `GET /api/users` - عرض جميع المستخدمين مع pagination
- `GET /api/users/{id}` - عرض مستخدم محدد
- `POST /api/users` - إنشاء مستخدم جديد
- `PUT /api/users/{id}` - تحديث مستخدم
- `DELETE /api/users/{id}` - حذف مستخدم
- `GET /api/users/{id}/relations` - عرض المستخدم مع علاقاته (عربات وفواتير)
- `GET /api/users/role/{role}` - عرض المستخدمين حسب الدور

**المعاملات المطلوبة**:
```php
// POST /api/users
{
    "name": "اسم المستخدم",
    "email": "user@example.com",
    "password": "كلمة المرور",
    "role": "admin|customer|manager"
}

// PUT /api/users/{id}
{
    "name": "اسم المستخدم الجديد", // اختياري
    "email": "newemail@example.com", // اختياري
    "password": "كلمة مرور جديدة", // اختياري
    "role": "admin|customer|manager" // اختياري
}
```

**أمثلة على الاستجابة**:
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "أحمد محمد",
        "email": "ahmed@example.com",
        "role": "customer",
        "created_at": "2024-01-01T10:00:00.000000Z",
        "updated_at": "2024-01-01T10:00:00.000000Z"
    },
    "message": "User created successfully"
}
```

### 2. ProductController
**الملف**: `app/Http/Controllers/ProductController.php`

**الـ Endpoints**:
- `GET /api/products` - عرض جميع المنتجات مع pagination
- `GET /api/products/{id}` - عرض منتج محدد
- `POST /api/products` - إنشاء منتج جديد
- `PUT /api/products/{id}` - تحديث منتج
- `DELETE /api/products/{id}` - حذف منتج
- `GET /api/products/search?query=اسم المنتج` - البحث في المنتجات
- `GET /api/products/price-range?min_price=10&max_price=100` - فلترة حسب السعر
- `GET /api/products/low-stock?threshold=10` - عرض المنتجات قليلة المخزون
- `PUT /api/products/{id}/stock` - تحديث مخزون المنتج

**المعاملات المطلوبة**:
```php
// POST /api/products (JSON)
{
    "name": "اسم المنتج",
    "description": "وصف المنتج",
    "price": 100.50,
    "stock": 50,
    "image": null // اختياري، للصور استخدم multipart/form-data
}

// POST /api/products (مع صورة - multipart/form-data)
// name: "اسم المنتج"
// description: "وصف المنتج"
// price: 100.50
// stock: 50
// image: [ملف الصورة]

// PUT /api/products/{id}
{
    "name": "اسم المنتج الجديد", // اختياري
    "description": "وصف جديد", // اختياري
    "price": 120.00, // اختياري
    "stock": 60, // اختياري
    "image": null // اختياري، للصور استخدم multipart/form-data
}
```

### 3. CartController
**الملف**: `app/Http/Controllers/CartController.php`

**الـ Endpoints**:
- `GET /api/carts` - عرض جميع العربات مع pagination
- `GET /api/carts/{id}` - عرض عربة محددة
- `POST /api/carts` - إنشاء عربة جديدة
- `DELETE /api/carts/{id}` - حذف عربة
- `GET /api/carts/user/{userId}` - عرض عربة المستخدم النشطة
- `POST /api/carts/{cartId}/add-product` - إضافة منتج للعربة
- `PUT /api/carts/items/{cartItemId}/quantity` - تحديث كمية المنتج في العربة
- `DELETE /api/carts/items/{cartItemId}` - حذف منتج من العربة
- `GET /api/carts/{cartId}/total` - حساب إجمالي العربة
- `POST /api/carts/{cartId}/clear` - تفريغ العربة
- `GET /api/carts/{cartId}/items-count` - عدد العناصر في العربة
- `GET /api/carts/{cartId}/validate-checkout` - التحقق من صحة العربة للدفع

**المعاملات المطلوبة**:
```php
// POST /api/carts
{
    "user_id": 1
}

// POST /api/carts/{cartId}/add-product
{
    "product_id": 1,
    "quantity": 2 // اختياري، الافتراضي 1
}

// PUT /api/carts/items/{cartItemId}/quantity
{
    "quantity": 5
}
```

### 4. InvoiceController
**الملف**: `app/Http/Controllers/InvoiceController.php`

**الـ Endpoints**:
- `GET /api/invoices` - عرض جميع الفواتير مع pagination
- `GET /api/invoices/{id}` - عرض فاتورة محددة
- `POST /api/invoices/from-cart` - إنشاء فاتورة من عربة
- `POST /api/invoices` - إنشاء فاتورة يدوياً
- `PUT /api/invoices/{id}/status` - تحديث حالة الفاتورة
- `DELETE /api/invoices/{id}` - حذف فاتورة
- `GET /api/invoices/user/{userId}` - عرض فواتير المستخدم
- `GET /api/invoices/status/{status}` - عرض الفواتير حسب الحالة
- `GET /api/invoices/date-range` - عرض الفواتير حسب النطاق الزمني
- `GET /api/invoices/sales-statistics` - إحصائيات المبيعات
- `GET /api/invoices/total-sales` - إجمالي المبيعات

**المعاملات المطلوبة**:
```php
// POST /api/invoices/from-cart
{
    "cart_id": 1,
    "status": "pending" // اختياري
}

// POST /api/invoices
{
    "user_id": 1,
    "items": [
        {
            "product_id": 1,
            "quantity": 2,
            "price": 100.50
        },
        {
            "product_id": 2,
            "quantity": 1,
            "price": 50.00
        }
    ],
    "status": "pending" // اختياري
}

// PUT /api/invoices/{id}/status
{
    "status": "completed|cancelled|pending"
}
```

## Services والـ Business Logic

### 1. UserService
**الملف**: `app/Services/UserService.php`

**الوظائف الرئيسية**:
- `getAllUsers($perPage)` - عرض جميع المستخدمين مع pagination
- `getUserById($id)` - الحصول على مستخدم محدد
- `createUser($data)` - إنشاء مستخدم جديد مع تشفير كلمة المرور
- `updateUser($id, $data)` - تحديث بيانات المستخدم
- `deleteUser($id)` - حذف المستخدم
- `getUserWithRelations($id)` - الحصول على المستخدم مع علاقاته
- `getUsersByRole($role)` - الحصول على المستخدمين حسب الدور

**أمثلة على الاستخدام**:
```php
// إنشاء مستخدم جديد
$userService = new UserService();
$user = $userService->createUser([
    'name' => 'أحمد محمد',
    'email' => 'ahmed@example.com',
    'password' => 'password123',
    'role' => 'customer'
]);

// الحصول على المستخدم مع علاقاته
$user = $userService->getUserWithRelations(1);
// يشمل: carts.cartItems.product, invoices.invoiceItems.product
```

### 2. ProductService
**الملف**: `app/Services/ProductService.php`

**الوظائف الرئيسية**:
- `getAllProducts($perPage)` - عرض جميع المنتجات
- `getProductById($id)` - الحصول على منتج محدد
- `createProduct($data)` - إنشاء منتج جديد
- `updateProduct($id, $data)` - تحديث المنتج
- `deleteProduct($id)` - حذف المنتج
- `getLowStockProducts($threshold)` - المنتجات قليلة المخزون
- `updateStock($id, $quantity)` - تحديث المخزون
- `searchProducts($query, $perPage)` - البحث في المنتجات
- `getProductsByPriceRange($minPrice, $maxPrice)` - فلترة حسب السعر

**أمثلة على الاستخدام**:
```php
// إنشاء منتج جديد
$productService = new ProductService();
$product = $productService->createProduct([
    'name' => 'لابتوب ديل',
    'description' => 'لابتوب عالي الأداء',
    'price' => 2500.00,
    'stock' => 10
]);

// البحث في المنتجات
$products = $productService->searchProducts('لابتوب', 10);
```

### 3. CartService
**الملف**: `app/Services/CartService.php`

**الوظائف الرئيسية**:
- `getAllCarts($perPage)` - عرض جميع العربات
- `getCartById($id)` - الحصول على عربة محددة
- `createCart($userId)` - إنشاء عربة جديدة للمستخدم
- `deleteCart($id)` - حذف العربة
- `getUserActiveCart($userId)` - الحصول على عربة المستخدم النشطة
- `addProductToCart($cartId, $productId, $quantity)` - إضافة منتج للعربة
- `updateCartItemQuantity($cartItemId, $quantity)` - تحديث كمية المنتج
- `removeProductFromCart($cartItemId)` - حذف منتج من العربة
- `calculateCartTotal($cartId)` - حساب إجمالي العربة
- `clearCart($cartId)` - تفريغ العربة
- `getCartItemsCount($cartId)` - عدد العناصر في العربة
- `validateCartForCheckout($cartId)` - التحقق من صحة العربة للدفع

**أمثلة على الاستخدام**:
```php
// إنشاء عربة جديدة للمستخدم
$cartService = new CartService();
$cart = $cartService->createCart(1);

// إضافة منتج للعربة
$cartItem = $cartService->addProductToCart(1, 2, 3); // cart_id=1, product_id=2, quantity=3

// حساب إجمالي العربة
$total = $cartService->calculateCartTotal(1); // يعيد 150.00 مثلاً

// التحقق من صحة العربة للدفع
$errors = $cartService->validateCartForCheckout(1);
if (empty($errors)) {
    // العربة صالحة للدفع
}
```

### 4. InvoiceService
**الملف**: `app/Services/InvoiceService.php`

**الوظائف الرئيسية**:
- `getAllInvoices($perPage)` - عرض جميع الفواتير
- `getInvoiceById($id)` - الحصول على فاتورة محددة
- `createInvoiceFromCart($cartId, $status)` - إنشاء فاتورة من عربة
- `createInvoice($userId, $items, $status)` - إنشاء فاتورة يدوياً
- `updateInvoiceStatus($id, $status)` - تحديث حالة الفاتورة
- `deleteInvoice($id)` - حذف الفاتورة
- `getUserInvoices($userId)` - فواتير المستخدم
- `getInvoicesByStatus($status)` - الفواتير حسب الحالة
- `getInvoicesByDateRange($startDate, $endDate)` - الفواتير حسب النطاق الزمني
- `calculateTotalSales($startDate, $endDate)` - حساب إجمالي المبيعات
- `getSalesStatistics($startDate, $endDate)` - إحصائيات المبيعات

**أمثلة على الاستخدام**:
```php
// إنشاء فاتورة من عربة
$invoiceService = new InvoiceService();
$invoice = $invoiceService->createInvoiceFromCart(1, 'completed');
// يتم تلقائياً: إنشاء الفاتورة، إنشاء عناصر الفاتورة، تحديث المخزون، تفريغ العربة

// إنشاء فاتورة يدوياً
$invoice = $invoiceService->createInvoice(1, [
    [
        'product_id' => 1,
        'quantity' => 2,
        'price' => 100.50
    ],
    [
        'product_id' => 2,
        'quantity' => 1,
        'price' => 50.00
    ]
], 'pending');

// الحصول على إحصائيات المبيعات
$stats = $invoiceService->getSalesStatistics('2024-01-01', '2024-12-31');
// يعيد: total_sales, total_invoices, average_invoice_value, pending_invoices, etc.
```

## أمثلة متقدمة على الاستخدام

### إنشاء عربة جديدة وإضافة منتجات إليها:
```php
// إنشاء عربة جديدة للمستخدم
$cart = $cartService->createCart(1);

// إضافة منتجات للعربة
$cartService->addProductToCart($cart->id, 1, 2); // منتج 1، كمية 2
$cartService->addProductToCart($cart->id, 2, 1); // منتج 2، كمية 1

// حساب الإجمالي
$total = $cartService->calculateCartTotal($cart->id);

// التحقق من صحة العربة
$errors = $cartService->validateCartForCheckout($cart->id);
```

### إنشاء فاتورة من عربة:
```php
// إنشاء فاتورة من عربة موجودة
$invoice = $invoiceService->createInvoiceFromCart($cartId, 'completed');

// تحديث حالة الفاتورة
$invoiceService->updateInvoiceStatus($invoice->id, 'completed');
```

### البحث والفلترة:
```php
// البحث في المنتجات
$products = $productService->searchProducts('لابتوب');

// فلترة المنتجات حسب السعر
$products = $productService->getProductsByPriceRange(1000, 3000);

// عرض المنتجات قليلة المخزون
$lowStockProducts = $productService->getLowStockProducts(5);
```

### إحصائيات المبيعات:
```php
// إحصائيات المبيعات للعام الحالي
$stats = $invoiceService->getSalesStatistics('2024-01-01', '2024-12-31');

// إجمالي المبيعات
$totalSales = $invoiceService->calculateTotalSales();

// فواتير المستخدم
$userInvoices = $invoiceService->getUserInvoices(1);
```

## ملاحظات مهمة

1. جميع الجداول تحتوي على timestamps تلقائية (created_at, updated_at)
2. تم استخدام DECIMAL(10,2) للأسعار لضمان دقة العمليات الحسابية
3. جميع العلاقات تستخدم CASCADE للحذف لضمان سلامة البيانات
4. البريد الإلكتروني في جدول المستخدمين فريد (UNIQUE)
5. يمكن إضافة فهارس إضافية لتحسين الأداء حسب الحاجة
6. جميع Models تستخدم HasFactory للاختبارات
7. تم تطبيق Type Casting للأسعار لضمان التعامل الصحيح مع الأرقام العشرية
8. جميع Controllers تستخدم Dependency Injection للـ Services عبر HotlineServiceProvider
9. تم تطبيق Validation شامل في جميع الـ endpoints
10. جميع العمليات الحساسة (مثل إنشاء الفواتير) تستخدم Database Transactions
11. تم تطبيق Error Handling شامل مع رسائل خطأ واضحة
12. جميع الـ responses تتبع نفس التنسيق: success, data, message
13. دعم رفع الصور للمنتجات مع تخزين المسار في قاعدة البيانات
14. الصور محفوظة في `storage/app/public/products/` ومتاحة عبر `asset('storage/products/filename')`
15. عند تحديث صورة منتج، يتم حذف الصورة القديمة تلقائياً
16. جميع استجابات المنتجات تتضمن `image_url` للوصول السهل للصور

## دعم الصور في المنتجات

### مواصفات الصور المدعومة:
- **الأنواع**: JPEG, PNG, JPG, GIF, SVG
- **الحجم الأقصى**: 2 ميجابايت (2048 كيلوبايت)
- **التخزين**: `storage/app/public/products/`
- **الوصول**: `asset('storage/products/filename')`

### مثال على الاستجابة مع الصورة:
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "منتج جديد",
        "description": "وصف المنتج",
        "price": "100.50",
        "stock": 10,
        "image": "products/abc123.jpg",
        "image_url": "http://localhost:8000/storage/products/abc123.jpg",
        "created_at": "2024-01-15T10:30:00.000000Z",
        "updated_at": "2024-01-15T10:30:00.000000Z"
    },
    "message": "Product created successfully"
}
```

### استخدام الصور في Laravel:
```php
// الحصول على رابط الصورة
$product = Product::find(1);
$imageUrl = $product->image_url; // يعيد الرابط الكامل للصورة

// التحقق من وجود صورة
if ($product->image) {
    // المنتج له صورة
    echo "صورة المنتج: " . $product->image_url;
} else {
    // المنتج بدون صورة
    echo "لا توجد صورة للمنتج";
}
```
