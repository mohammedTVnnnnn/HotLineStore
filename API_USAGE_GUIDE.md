# دليل استخدام API - نظام HotLine للتجارة الإلكترونية

## نظرة عامة
هذا الدليل يوضح كيفية استخدام API الخاص بنظام HotLine للتجارة الإلكترونية. النظام يدعم إدارة المستخدمين والمنتجات وعربات التسوق والفواتير.

## البدء السريع

### 1. تشغيل المشروع
```bash
# تثبيت التبعيات
composer install

# تشغيل المايجريشن
php artisan migrate

# تشغيل الخادم
php artisan serve
```

### 2. قاعدة URL للـ API
```
http://localhost:8000/api
```

## أمثلة عملية على الاستخدام

### 1. إدارة المستخدمين

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

#### الحصول على جميع المستخدمين
```bash
curl -X GET http://localhost:8000/api/users
```

#### الحصول على مستخدم محدد
```bash
curl -X GET http://localhost:8000/api/users/1
```

### 2. إدارة المنتجات

#### إنشاء منتج جديد (Admin فقط)
```bash
# إنشاء منتج بدون صورة
curl -X POST http://localhost:8000/api/products \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "name": "لابتوب ديل",
    "description": "لابتوب عالي الأداء",
    "price": 2500.00,
    "stock": 10
  }'

# إنشاء منتج مع صورة
curl -X POST http://localhost:8000/api/products \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "name=لابتوب ديل" \
  -F "description=لابتوب عالي الأداء" \
  -F "price=2500.00" \
  -F "stock=10" \
  -F "image=@/path/to/image.jpg"
```

**ملاحظة**: يتطلب هذا الـ endpoint مصادقة وتصريح admin.

#### الحصول على جميع المنتجات
```bash
curl -X GET http://localhost:8000/api/products
```

#### الحصول على منتج محدد
```bash
curl -X GET http://localhost:8000/api/products/1
```

#### البحث في المنتجات
```bash
curl -X GET "http://localhost:8000/api/products/search?query=لابتوب"
```

#### فلترة المنتجات حسب السعر
```bash
curl -X GET "http://localhost:8000/api/products/price-range?min_price=1000&max_price=3000"
```

#### عرض المنتجات قليلة المخزون
```bash
curl -X GET "http://localhost:8000/api/products/low-stock?threshold=5"
```

#### تحديث منتج (Admin فقط)
```bash
# تحديث منتج بدون صورة
curl -X PUT http://localhost:8000/api/products/1 \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "name": "لابتوب ديل محدث",
    "description": "لابتوب عالي الأداء مع مواصفات جديدة",
    "price": 2800.00,
    "stock": 15
  }'

# تحديث منتج مع صورة جديدة
curl -X PUT http://localhost:8000/api/products/1 \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "name=لابتوب ديل محدث" \
  -F "description=لابتوب عالي الأداء مع مواصفات جديدة" \
  -F "price=2800.00" \
  -F "stock=15" \
  -F "image=@/path/to/new-image.jpg"
```

#### تحديث مخزون منتج (Admin فقط)
```bash
curl -X PUT http://localhost:8000/api/products/1/stock \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "stock": 20
  }'
```

#### حذف منتج (Admin فقط)
```bash
curl -X DELETE http://localhost:8000/api/products/1 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**ملاحظة**: جميع عمليات التعديل والحذف تتطلب مصادقة وتصريح admin.

### دعم الصور في المنتجات

#### مواصفات الصور المدعومة:
- **الأنواع**: JPEG, PNG, JPG, GIF, SVG
- **الحجم الأقصى**: 2 ميجابايت (2048 كيلوبايت)
- **التخزين**: يتم حفظ الصور في `storage/app/public/products/`
- **الوصول**: الصور متاحة عبر رابط `asset('storage/products/filename')`

#### أمثلة على الاستجابة مع الصور:
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

#### ملاحظات مهمة:
- عند رفع صورة جديدة، يتم حذف الصورة القديمة تلقائياً
- إذا لم يتم رفع صورة، القيمة `image` ستكون `null`
- رابط الصورة (`image_url`) متاح في جميع استجابات المنتجات
- يجب استخدام `multipart/form-data` عند رفع الصور

### 3. إدارة العربات

#### إنشاء عربة جديدة للمستخدم
```bash
curl -X POST http://localhost:8000/api/carts \
  -H "Content-Type: application/json" \
  -d '{
    "user_id": 1
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

#### حساب إجمالي العربة
```bash
curl -X GET http://localhost:8000/api/carts/1/total
```

#### التحقق من صحة العربة للدفع
```bash
curl -X GET http://localhost:8000/api/carts/1/validate-checkout
```

### 4. إدارة الفواتير

#### إنشاء فاتورة من عربة
```bash
curl -X POST http://localhost:8000/api/invoices/from-cart \
  -H "Content-Type: application/json" \
  -d '{
    "cart_id": 1,
    "status": "completed"
  }'
```

#### إنشاء فاتورة يدوياً
```bash
curl -X POST http://localhost:8000/api/invoices \
  -H "Content-Type: application/json" \
  -d '{
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
    "status": "pending"
  }'
```

#### الحصول على إحصائيات المبيعات
```bash
curl -X GET "http://localhost:8000/api/invoices/sales-statistics?start_date=2024-01-01&end_date=2024-12-31"
```

## سيناريوهات عملية كاملة

### السيناريو 1: عملية شراء كاملة

1. **إنشاء مستخدم جديد**
```bash
curl -X POST http://localhost:8000/api/users \
  -H "Content-Type: application/json" \
  -d '{
    "name": "سارة أحمد",
    "email": "sara@example.com",
    "password": "password123",
    "role": "customer"
  }'
```

2. **إنشاء منتجات**
```bash
# منتج 1 مع صورة
curl -X POST http://localhost:8000/api/products \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN" \
  -F "name=هاتف آيفون" \
  -F "description=هاتف ذكي حديث" \
  -F "price=3000.00" \
  -F "stock=5" \
  -F "image=@/path/to/iphone.jpg"

# منتج 2 بدون صورة
curl -X POST http://localhost:8000/api/products \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN" \
  -d '{
    "name": "سماعات لاسلكية",
    "description": "سماعات عالية الجودة",
    "price": 200.00,
    "stock": 20
  }'
```

3. **إنشاء عربة للمستخدم**
```bash
curl -X POST http://localhost:8000/api/carts \
  -H "Content-Type: application/json" \
  -d '{
    "user_id": 1
  }'
```

4. **إضافة منتجات للعربة**
```bash
# إضافة هاتف آيفون
curl -X POST http://localhost:8000/api/carts/1/add-product \
  -H "Content-Type: application/json" \
  -d '{
    "product_id": 1,
    "quantity": 1
  }'

# إضافة سماعات
curl -X POST http://localhost:8000/api/carts/1/add-product \
  -H "Content-Type: application/json" \
  -d '{
    "product_id": 2,
    "quantity": 2
  }'
```

5. **حساب إجمالي العربة**
```bash
curl -X GET http://localhost:8000/api/carts/1/total
```

6. **التحقق من صحة العربة**
```bash
curl -X GET http://localhost:8000/api/carts/1/validate-checkout
```

7. **إنشاء الفاتورة**
```bash
curl -X POST http://localhost:8000/api/invoices/from-cart \
  -H "Content-Type: application/json" \
  -d '{
    "cart_id": 1,
    "status": "completed"
  }'
```

### السيناريو 2: إدارة المخزون

1. **عرض المنتجات قليلة المخزون**
```bash
curl -X GET "http://localhost:8000/api/products/low-stock?threshold=5"
```

2. **تحديث مخزون منتج**
```bash
curl -X PUT http://localhost:8000/api/products/1/stock \
  -H "Content-Type: application/json" \
  -d '{
    "stock": 15
  }'
```

### السيناريو 3: تقارير المبيعات

1. **إحصائيات المبيعات للشهر الحالي**
```bash
curl -X GET "http://localhost:8000/api/invoices/sales-statistics?start_date=2024-01-01&end_date=2024-01-31"
```

2. **فواتير المستخدم**
```bash
curl -X GET http://localhost:8000/api/invoices/user/1
```

3. **الفواتير المعلقة**
```bash
curl -X GET http://localhost:8000/api/invoices/status/pending
```

## رموز الاستجابة

### رموز النجاح
- `200` - تم بنجاح
- `201` - تم الإنشاء بنجاح

### رموز الخطأ
- `400` - طلب خاطئ
- `404` - غير موجود
- `422` - خطأ في التحقق من البيانات
- `500` - خطأ في الخادم

## تنسيق الاستجابة

جميع الاستجابات تتبع نفس التنسيق:

### استجابة نجاح
```json
{
    "success": true,
    "data": {
        // البيانات المطلوبة
    },
    "message": "رسالة النجاح"
}
```

### استجابة خطأ
```json
{
    "success": false,
    "message": "رسالة الخطأ",
    "errors": {
        // تفاصيل الأخطاء (في حالة وجود أخطاء في التحقق)
    }
}
```

## نصائح للاستخدام

1. **استخدم Pagination**: جميع endpoints التي تعرض قوائم تدعم pagination عبر المعامل `per_page`
2. **تحقق من المخزون**: قبل إضافة منتج للعربة، تأكد من توفر المخزون الكافي
3. **استخدم Transactions**: عمليات إنشاء الفواتير تستخدم Database Transactions لضمان سلامة البيانات
4. **تحقق من صحة العربة**: قبل إنشاء الفاتورة، استخدم endpoint التحقق من صحة العربة
5. **إدارة الأدوار**: النظام يدعم ثلاثة أدوار: admin, customer, manager

## اختبار النظام

يمكنك استخدام Postman أو أي أداة أخرى لاختبار الـ API. تأكد من:
- إرسال Content-Type: application/json في الـ headers
- استخدام HTTP methods الصحيحة (GET, POST, PUT, DELETE)
- إرسال البيانات في تنسيق JSON صحيح

## إدارة المنتجات - Products

### نظرة عامة
نظام إدارة المنتجات يوفر endpoints شاملة لإدارة المنتجات في النظام. بعض العمليات متاحة للجميع (عرض المنتجات) بينما العمليات الحساسة (إضافة، تعديل، حذف) تتطلب صلاحيات admin.

### الـ Endpoints المتاحة

#### 1. عرض المنتجات (متاح للجميع)
- **GET** `/api/products` - عرض جميع المنتجات مع pagination
- **GET** `/api/products/{id}` - عرض منتج محدد
- **GET** `/api/products/search` - البحث في المنتجات
- **GET** `/api/products/price-range` - فلترة حسب نطاق السعر
- **GET** `/api/products/low-stock` - عرض المنتجات قليلة المخزون

#### 2. إدارة المنتجات (Admin فقط)
- **POST** `/api/products` - إضافة منتج جديد
- **PUT** `/api/products/{id}` - تحديث منتج موجود
- **PUT** `/api/products/{id}/stock` - تحديث مخزون منتج
- **DELETE** `/api/products/{id}` - حذف منتج

### أمثلة على الاستخدام

#### إنشاء منتج جديد
```bash
curl -X POST http://localhost:8000/api/products \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "name": "هاتف آيفون 15",
    "description": "هاتف ذكي حديث من آبل",
    "price": 4000.00,
    "stock": 25
  }'
```

**Response (201 Created):**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "هاتف آيفون 15",
        "description": "هاتف ذكي حديث من آبل",
        "price": "4000.00",
        "stock": 25,
        "created_at": "2024-01-15T10:30:00.000000Z",
        "updated_at": "2024-01-15T10:30:00.000000Z"
    },
    "message": "Product created successfully"
}
```

#### تحديث منتج
```bash
curl -X PUT http://localhost:8000/api/products/1 \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "name": "هاتف آيفون 15 Pro",
    "price": 4500.00
  }'
```

#### تحديث المخزون فقط
```bash
curl -X PUT http://localhost:8000/api/products/1/stock \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "stock": 30
  }'
```

#### البحث في المنتجات
```bash
curl -X GET "http://localhost:8000/api/products/search?query=آيفون&per_page=10"
```

#### فلترة حسب السعر
```bash
curl -X GET "http://localhost:8000/api/products/price-range?min_price=1000&max_price=5000"
```

#### عرض المنتجات قليلة المخزون
```bash
curl -X GET "http://localhost:8000/api/products/low-stock?threshold=5"
```

### Validation Rules

#### عند إنشاء منتج جديد:
- `name`: مطلوب، نص، أقصى 255 حرف
- `description`: مطلوب، نص
- `price`: مطلوب، رقم، أكبر من أو يساوي 0
- `stock`: مطلوب، عدد صحيح، أكبر من أو يساوي 0

#### عند تحديث منتج:
- جميع الحقول اختيارية (optional)
- نفس قواعد التحقق المذكورة أعلاه

### رموز الاستجابة

#### نجاح العمليات:
- `200` - تم بنجاح (تحديث، حذف)
- `201` - تم الإنشاء بنجاح

#### أخطاء:
- `400` - طلب خاطئ
- `401` - غير مصرح (لم يتم تسجيل الدخول)
- `403` - ممنوع (ليس لديك صلاحيات admin)
- `404` - المنتج غير موجود
- `422` - خطأ في التحقق من البيانات

### أمثلة على الأخطاء

#### خطأ في التحقق من البيانات (422):
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "name": ["The name field is required."],
        "price": ["The price must be at least 0."]
    }
}
```

#### خطأ في الصلاحيات (403):
```json
{
    "success": false,
    "message": "Access denied. Admin role required."
}
```

#### منتج غير موجود (404):
```json
{
    "success": false,
    "message": "Product not found"
}
```

## نظام السلة - Cart System

### نظرة عامة
نظام السلة الجديد يوفر إدارة كاملة لعربة التسوق الخاصة بكل مستخدم مصادق عليه. النظام يدعم إضافة المنتجات، تعديل الكميات، وحذف العناصر مع حماية كاملة بالـ middleware.

### الـ Endpoints المتاحة

#### 1. عرض السلة
- **GET** `/api/cart` - عرض محتويات السلة الخاصة بالمستخدم الحالي
- **المتطلبات**: مصادقة مطلوبة (auth:sanctum)
- **الاستجابة**: السلة مع المنتجات والإجمالي وعدد العناصر

#### 2. إضافة منتج للسلة
- **POST** `/api/cart` - إضافة منتج جديد للسلة أو زيادة كمية منتج موجود
- **المتطلبات**: مصادقة مطلوبة (auth:sanctum)
- **المعاملات**: product_id (مطلوب), quantity (مطلوب)

#### 3. تعديل كمية منتج
- **PUT** `/api/cart/{id}` - تعديل كمية منتج محدد في السلة
- **المتطلبات**: مصادقة مطلوبة (auth:sanctum)
- **المعاملات**: quantity (مطلوب)

#### 4. حذف منتج من السلة
- **DELETE** `/api/cart/{id}` - حذف منتج من السلة
- **المتطلبات**: مصادقة مطلوبة (auth:sanctum)

### أمثلة على الاستخدام

#### عرض السلة
```bash
curl -X GET http://localhost:8000/api/cart \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Response (200 OK):**
```json
{
    "success": true,
    "data": {
        "cart": {
            "id": 1,
            "user_id": 1,
            "created_at": "2024-01-15T10:30:00.000000Z",
            "updated_at": "2024-01-15T10:30:00.000000Z",
            "cart_items": [
                {
                    "id": 1,
                    "cart_id": 1,
                    "product_id": 1,
                    "quantity": 2,
                    "product": {
                        "id": 1,
                        "name": "هاتف آيفون 15",
                        "description": "هاتف ذكي حديث من آبل",
                        "price": "4000.00",
                        "stock": 25
                    }
                }
            ]
        },
        "total": 8000.00,
        "items_count": 2
    },
    "message": "تم عرض محتويات السلة بنجاح"
}
```

#### إضافة منتج للسلة
```bash
curl -X POST http://localhost:8000/api/cart \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "product_id": 1,
    "quantity": 2
  }'
```

**Response (200 OK):**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "cart_id": 1,
        "product_id": 1,
        "quantity": 2,
        "product": {
            "id": 1,
            "name": "هاتف آيفون 15",
            "description": "هاتف ذكي حديث من آبل",
            "price": "4000.00",
            "stock": 25
        }
    },
    "message": "تم إضافة المنتج إلى السلة بنجاح"
}
```

#### تعديل كمية منتج
```bash
curl -X PUT http://localhost:8000/api/cart/1 \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "quantity": 3
  }'
```

#### حذف منتج من السلة
```bash
curl -X DELETE http://localhost:8000/api/cart/1 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Validation Rules

#### عند إضافة منتج للسلة:
- `product_id`: مطلوب، عدد صحيح، يجب أن يكون موجود في جدول products
- `quantity`: مطلوب، عدد صحيح، يجب أن يكون أكبر من أو يساوي 1

#### عند تعديل كمية منتج:
- `quantity`: مطلوب، عدد صحيح، يجب أن يكون أكبر من أو يساوي 1

### الميزات الخاصة

#### 1. إدارة تلقائية للسلة
- النظام ينشئ سلة جديدة تلقائياً للمستخدم عند الحاجة
- كل مستخدم له سلة واحدة نشطة (الأحدث)

#### 2. منع التكرار
- عند إضافة منتج موجود بالفعل، يتم زيادة الكمية بدلاً من إنشاء عنصر جديد
- التحقق من المخزون المتاح قبل الإضافة أو التعديل

#### 3. الحماية والأمان
- جميع العمليات محمية بـ auth:sanctum middleware
- المستخدم يمكنه الوصول فقط لعناصر سلة الخاصة به
- التحقق من صحة البيانات في كل عملية

#### 4. حساب الإجمالي التلقائي
- النظام يحسب إجمالي السلة تلقائياً
- يعرض عدد العناصر الإجمالي

### رموز الاستجابة

#### نجاح العمليات:
- `200` - تم بنجاح (عرض، إضافة، تعديل، حذف)

#### أخطاء:
- `401` - غير مصرح (لم يتم تسجيل الدخول)
- `400` - طلب خاطئ (مخزون غير كافي)
- `404` - غير موجود (منتج أو عنصر سلة غير موجود)
- `422` - خطأ في التحقق من البيانات

### أمثلة على الأخطاء

#### خطأ في التحقق من البيانات (422):
```json
{
    "success": false,
    "message": "فشل في التحقق من البيانات",
    "errors": {
        "product_id": ["The product id field is required."],
        "quantity": ["The quantity must be at least 1."]
    }
}
```

#### خطأ في المخزون (400):
```json
{
    "success": false,
    "message": "الكمية المطلوبة غير متوفرة في المخزون"
}
```

#### خطأ في المصادقة (401):
```json
{
    "success": false,
    "message": "المستخدم غير مصادق عليه"
}
```

### سيناريو كامل: عملية تسوق

1. **عرض السلة الحالية**
```bash
curl -X GET http://localhost:8000/api/cart \
  -H "Authorization: Bearer YOUR_TOKEN"
```

2. **إضافة منتج للسلة**
```bash
curl -X POST http://localhost:8000/api/cart \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "product_id": 1,
    "quantity": 1
  }'
```

3. **إضافة منتج آخر**
```bash
curl -X POST http://localhost:8000/api/cart \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "product_id": 2,
    "quantity": 2
  }'
```

4. **تعديل كمية منتج**
```bash
curl -X PUT http://localhost:8000/api/cart/1 \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "quantity": 3
  }'
```

5. **عرض السلة المحدثة**
```bash
curl -X GET http://localhost:8000/api/cart \
  -H "Authorization: Bearer YOUR_TOKEN"
```

6. **حذف منتج من السلة**
```bash
curl -X DELETE http://localhost:8000/api/cart/2 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## نظام الفواتير - Invoices

### نظرة عامة
نظام الفواتير الجديد يوفر إدارة كاملة للفواتير الخاصة بكل مستخدم مصادق عليه. النظام يدعم إنشاء فواتير من السلة، عرض الفواتير، وتفاصيل الفواتير مع حماية كاملة بالـ middleware.

### الـ Endpoints المتاحة

#### 1. إنشاء فاتورة من السلة (Checkout)
- **POST** `/api/checkout` - تحويل محتويات السلة الحالية إلى فاتورة جديدة
- **المتطلبات**: مصادقة مطلوبة (auth:sanctum)
- **المعاملات**: status (اختياري) - pending, completed, cancelled
- **الاستجابة**: الفاتورة الجديدة مع تفاصيل المنتجات

#### 2. عرض فواتير المستخدم
- **GET** `/api/invoices` - عرض قائمة الفواتير الخاصة بالمستخدم الحالي
- **المتطلبات**: مصادقة مطلوبة (auth:sanctum)
- **المعاملات**: per_page (اختياري) - عدد الفواتير في الصفحة الواحدة
- **الاستجابة**: قائمة الفواتير مع pagination

#### 3. عرض تفاصيل فاتورة
- **GET** `/api/invoices/{id}` - عرض تفاصيل فاتورة معينة مع المنتجات
- **المتطلبات**: مصادقة مطلوبة (auth:sanctum)
- **الاستجابة**: تفاصيل الفاتورة مع المنتجات المرتبطة

### أمثلة على الاستخدام

#### إنشاء فاتورة من السلة (Checkout)
```bash
curl -X POST http://localhost:8000/api/checkout \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "status": "completed"
  }'
```

**Response (201 Created):**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "user_id": 1,
        "total": "8400.00",
        "status": "completed",
        "created_at": "2024-01-15T10:30:00.000000Z",
        "updated_at": "2024-01-15T10:30:00.000000Z",
        "user": {
            "id": 1,
            "name": "أحمد محمد",
            "email": "ahmed@example.com",
            "role": "customer"
        },
        "invoice_items": [
            {
                "id": 1,
                "invoice_id": 1,
                "product_id": 1,
                "quantity": 2,
                "price": "4000.00",
                "product": {
                    "id": 1,
                    "name": "هاتف آيفون 15",
                    "description": "هاتف ذكي حديث من آبل",
                    "price": "4000.00",
                    "stock": 23
                }
            },
            {
                "id": 2,
                "invoice_id": 1,
                "product_id": 2,
                "quantity": 2,
                "price": "200.00",
                "product": {
                    "id": 2,
                    "name": "سماعات لاسلكية",
                    "description": "سماعات عالية الجودة",
                    "price": "200.00",
                    "stock": 18
                }
            }
        ]
    },
    "message": "تم إنشاء الفاتورة بنجاح من السلة"
}
```

#### عرض فواتير المستخدم
```bash
curl -X GET http://localhost:8000/api/invoices?per_page=10 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Response (200 OK):**
```json
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "user_id": 1,
                "total": "8400.00",
                "status": "completed",
                "created_at": "2024-01-15T10:30:00.000000Z",
                "updated_at": "2024-01-15T10:30:00.000000Z",
                "invoice_items": [
                    {
                        "id": 1,
                        "invoice_id": 1,
                        "product_id": 1,
                        "quantity": 2,
                        "price": "4000.00",
                        "product": {
                            "id": 1,
                            "name": "هاتف آيفون 15",
                            "description": "هاتف ذكي حديث من آبل",
                            "price": "4000.00"
                        }
                    }
                ]
            }
        ],
        "first_page_url": "http://localhost:8000/api/invoices?page=1",
        "from": 1,
        "last_page": 1,
        "last_page_url": "http://localhost:8000/api/invoices?page=1",
        "links": [...],
        "next_page_url": null,
        "path": "http://localhost:8000/api/invoices",
        "per_page": 10,
        "prev_page_url": null,
        "to": 1,
        "total": 1
    },
    "message": "تم عرض فواتير المستخدم بنجاح"
}
```

#### عرض تفاصيل فاتورة معينة
```bash
curl -X GET http://localhost:8000/api/invoices/1 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Response (200 OK):**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "user_id": 1,
        "total": "8400.00",
        "status": "completed",
        "created_at": "2024-01-15T10:30:00.000000Z",
        "updated_at": "2024-01-15T10:30:00.000000Z",
        "user": {
            "id": 1,
            "name": "أحمد محمد",
            "email": "ahmed@example.com",
            "role": "customer"
        },
        "invoice_items": [
            {
                "id": 1,
                "invoice_id": 1,
                "product_id": 1,
                "quantity": 2,
                "price": "4000.00",
                "product": {
                    "id": 1,
                    "name": "هاتف آيفون 15",
                    "description": "هاتف ذكي حديث من آبل",
                    "price": "4000.00",
                    "stock": 23
                }
            }
        ]
    },
    "message": "تم عرض تفاصيل الفاتورة بنجاح"
}
```

### Validation Rules

#### عند إنشاء فاتورة من السلة:
- `status`: اختياري، نص، يجب أن يكون واحد من: pending, completed, cancelled

### الميزات الخاصة

#### 1. عملية Checkout المتكاملة
- جلب السلة النشطة للمستخدم تلقائياً
- التحقق من وجود منتجات في السلة
- التحقق من توفر المخزون لجميع المنتجات
- إنشاء فاتورة جديدة مع حساب الإجمالي
- نسخ المنتجات من السلة إلى الفاتورة مع السعر وقت الشراء
- تقليل المخزون في جدول المنتجات
- تفريغ السلة بعد إنشاء الفاتورة
- تنفيذ العملية داخل Database Transaction

#### 2. الحماية والأمان
- جميع العمليات محمية بـ auth:sanctum middleware
- المستخدم يمكنه الوصول فقط لفواتيره الخاصة
- التحقق من ملكية الفواتير قبل العرض

#### 3. إدارة المخزون التلقائية
- تقليل المخزون تلقائياً عند إنشاء الفاتورة
- التحقق من توفر الكمية المطلوبة قبل الإنشاء
- منع إنشاء فاتورة إذا كان المخزون غير كافي

#### 4. حفظ تاريخ الأسعار
- حفظ سعر المنتج وقت الشراء في الفاتورة
- حتى لو تغير سعر المنتج لاحقاً، الفاتورة تحتفظ بالسعر الأصلي

### رموز الاستجابة

#### نجاح العمليات:
- `200` - تم بنجاح (عرض الفواتير)
- `201` - تم الإنشاء بنجاح (إنشاء فاتورة)

#### أخطاء:
- `401` - غير مصرح (لم يتم تسجيل الدخول)
- `400` - طلب خاطئ (سلة فارغة، مخزون غير كافي)
- `404` - غير موجود (لا توجد سلة، فاتورة غير موجودة)
- `422` - خطأ في التحقق من البيانات

### أمثلة على الأخطاء

#### خطأ في التحقق من البيانات (422):
```json
{
    "success": false,
    "message": "فشل في التحقق من البيانات",
    "errors": {
        "status": ["The selected status is invalid."]
    }
}
```

#### خطأ في المخزون (400):
```json
{
    "success": false,
    "message": "الكمية المطلوبة غير متوفرة للمنتج: هاتف آيفون 15"
}
```

#### سلة فارغة (400):
```json
{
    "success": false,
    "message": "السلة فارغة، لا يمكن إنشاء فاتورة"
}
```

#### خطأ في المصادقة (401):
```json
{
    "success": false,
    "message": "المستخدم غير مصادق عليه"
}
```

### سيناريو كامل: عملية تسوق مع Checkout

1. **عرض السلة الحالية**
```bash
curl -X GET http://localhost:8000/api/cart \
  -H "Authorization: Bearer YOUR_TOKEN"
```

2. **إضافة منتجات للسلة**
```bash
curl -X POST http://localhost:8000/api/cart \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{"product_id": 1, "quantity": 2}'

curl -X POST http://localhost:8000/api/cart \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{"product_id": 2, "quantity": 1}'
```

3. **عرض السلة المحدثة**
```bash
curl -X GET http://localhost:8000/api/cart \
  -H "Authorization: Bearer YOUR_TOKEN"
```

4. **إنشاء فاتورة من السلة (Checkout)**
```bash
curl -X POST http://localhost:8000/api/checkout \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{"status": "completed"}'
```

5. **عرض فواتير المستخدم**
```bash
curl -X GET http://localhost:8000/api/invoices \
  -H "Authorization: Bearer YOUR_TOKEN"
```

6. **عرض تفاصيل الفاتورة الجديدة**
```bash
curl -X GET http://localhost:8000/api/invoices/1 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### نصائح للاستخدام

1. **استخدم Checkout بحذر**: العملية تقلل المخزون وتفريغ السلة نهائياً
2. **تحقق من السلة قبل Checkout**: تأكد من وجود المنتجات المطلوبة
3. **استخدم Pagination**: endpoint عرض الفواتير يدعم pagination
4. **احفظ رقم الفاتورة**: للرجوع إليها لاحقاً
5. **تحقق من حالة الفاتورة**: يمكنك تحديد حالة الفاتورة (pending, completed, cancelled)

## لوحة تحكم الأدمن - Admin Dashboard

### نظرة عامة
لوحة تحكم الأدمن توفر إمكانيات متقدمة لإدارة النظام ومراقبة الأداء. جميع الـ endpoints الخاصة بالأدمن محمية بـ middleware مزدوج:
- `auth:sanctum`: للتأكد من تسجيل دخول المستخدم
- `admin`: للتأكد من أن المستخدم لديه صلاحيات admin

### الـ Endpoints المتاحة

#### 1. عرض جميع الفواتير
- **GET** `/admin/dashboard/invoices` - عرض جميع الفواتير مع تفاصيل كاملة
- **المتطلبات**: مصادقة مطلوبة + صلاحيات admin
- **الاستجابة**: قائمة الفواتير مع بيانات المستخدمين والمنتجات المرتبطة

#### 2. فلترة الفواتير
- **GET** `/admin/dashboard/invoices/filter` - فلترة الفواتير حسب معايير محددة
- **المتطلبات**: مصادقة مطلوبة + صلاحيات admin
- **المعاملات**: from, to, user_id, status, per_page

#### 3. تقارير المبيعات
- **GET** `/admin/dashboard/reports/sales` - تقرير مبيعات يومي أو شهري
- **المتطلبات**: مصادقة مطلوبة + صلاحيات admin
- **المعاملات**: type (مطلوب), date, year, month

### أمثلة على الاستخدام

#### عرض جميع الفواتير
```bash
curl -X GET http://localhost:8000/admin/dashboard/invoices \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN"
```

**Response (200 OK):**
```json
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "user_id": 1,
                "total": "8400.00",
                "status": "completed",
                "created_at": "2024-01-15T10:30:00.000000Z",
                "updated_at": "2024-01-15T10:30:00.000000Z",
                "user": {
                    "id": 1,
                    "name": "أحمد محمد",
                    "email": "ahmed@example.com",
                    "role": "customer"
                },
                "invoice_items": [
                    {
                        "id": 1,
                        "invoice_id": 1,
                        "product_id": 1,
                        "quantity": 2,
                        "price": "4000.00",
                        "product": {
                            "id": 1,
                            "name": "هاتف آيفون 15",
                            "description": "هاتف ذكي حديث من آبل",
                            "price": "4000.00",
                            "stock": 23
                        }
                    }
                ]
            }
        ],
        "first_page_url": "http://localhost:8000/admin/dashboard/invoices?page=1",
        "from": 1,
        "last_page": 1,
        "last_page_url": "http://localhost:8000/admin/dashboard/invoices?page=1",
        "links": [...],
        "next_page_url": null,
        "path": "http://localhost:8000/admin/dashboard/invoices",
        "per_page": 15,
        "prev_page_url": null,
        "to": 1,
        "total": 1
    },
    "message": "تم عرض جميع الفواتير بنجاح"
}
```

#### فلترة الفواتير حسب التاريخ
```bash
curl -X GET "http://localhost:8000/admin/dashboard/invoices/filter?from=2024-01-01&to=2024-01-31" \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN"
```

#### فلترة الفواتير حسب المستخدم
```bash
curl -X GET "http://localhost:8000/admin/dashboard/invoices/filter?user_id=1" \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN"
```

#### فلترة الفواتير حسب الحالة
```bash
curl -X GET "http://localhost:8000/admin/dashboard/invoices/filter?status=completed" \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN"
```

**Response (200 OK):**
```json
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [...],
        "links": [...],
        "total": 5
    },
    "message": "تم فلترة الفواتير بنجاح",
    "filters_applied": {
        "status": "completed"
    }
}
```

#### تقرير المبيعات اليومي
```bash
curl -X GET "http://localhost:8000/admin/dashboard/reports/sales?type=daily&date=2024-01-15" \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN"
```

**Response (200 OK):**
```json
{
    "success": true,
    "data": {
        "period": "2024-01-15",
        "type": "daily",
        "summary": {
            "total_invoices": 15,
            "total_sales": 45000.00,
            "unique_customers": 12,
            "average_invoice_value": 3000.00
        },
        "top_customers": [
            {
                "user_id": 1,
                "total_spent": "15000.00",
                "invoice_count": 3,
                "user": {
                    "id": 1,
                    "name": "أحمد محمد",
                    "email": "ahmed@example.com"
                }
            }
        ],
        "top_products": [
            {
                "product_id": 1,
                "total_quantity": 8,
                "total_revenue": "32000.00",
                "product": {
                    "id": 1,
                    "name": "هاتف آيفون 15",
                    "description": "هاتف ذكي حديث من آبل"
                }
            }
        ],
        "generated_at": "2024-01-15T14:30:00"
    },
    "message": "تم إنشاء تقرير المبيعات daily بنجاح",
    "report_type": "daily",
    "period": "2024-01-15"
}
```

#### تقرير المبيعات الشهري
```bash
curl -X GET "http://localhost:8000/admin/dashboard/reports/sales?type=monthly&year=2024&month=1" \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN"
```

**Response (200 OK):**
```json
{
    "success": true,
    "data": {
        "period": "2024-01",
        "type": "monthly",
        "summary": {
            "total_invoices": 150,
            "total_sales": 450000.00,
            "unique_customers": 85,
            "average_invoice_value": 3000.00
        },
        "top_customers": [
            {
                "user_id": 1,
                "total_spent": "45000.00",
                "invoice_count": 15,
                "user": {
                    "id": 1,
                    "name": "أحمد محمد",
                    "email": "ahmed@example.com"
                }
            }
        ],
        "top_products": [
            {
                "product_id": 1,
                "total_quantity": 120,
                "total_revenue": "480000.00",
                "product": {
                    "id": 1,
                    "name": "هاتف آيفون 15",
                    "description": "هاتف ذكي حديث من آبل"
                }
            }
        ],
        "generated_at": "2024-01-31T23:59:59"
    },
    "message": "تم إنشاء تقرير المبيعات monthly بنجاح",
    "report_type": "monthly",
    "period": "2024-01"
}
```

### Validation Rules

#### فلترة الفواتير:
- `from`: اختياري، تاريخ صحيح
- `to`: اختياري، تاريخ صحيح، يجب أن يكون بعد أو يساوي `from`
- `user_id`: اختياري، عدد صحيح، يجب أن يكون موجود في جدول users
- `status`: اختياري، نص، يجب أن يكون واحد من: pending, completed, cancelled
- `per_page`: اختياري، عدد صحيح، بين 1 و 100

#### تقارير المبيعات:
- `type`: مطلوب، نص، يجب أن يكون واحد من: daily, monthly
- `date`: اختياري، تاريخ صحيح (للتقرير اليومي)
- `year`: اختياري، عدد صحيح، بين 2020 و 2030 (للتقرير الشهري)
- `month`: اختياري، عدد صحيح، بين 1 و 12 (للتقرير الشهري)

### الميزات الخاصة

#### 1. حماية كاملة
- جميع العمليات محمية بـ auth:sanctum middleware
- التحقق من صلاحيات admin قبل تنفيذ أي عملية
- رسائل خطأ واضحة للصلاحيات المفقودة

#### 2. فلترة متقدمة
- فلترة حسب نطاق التاريخ (من - إلى)
- فلترة حسب مستخدم محدد
- فلترة حسب حالة الفاتورة
- دعم pagination مع عدد عناصر قابل للتخصيص

#### 3. تقارير شاملة
- تقارير يومية وشهرية
- إحصائيات مفصلة: عدد الفواتير، إجمالي المبيعات، عدد العملاء، متوسط قيمة الفاتورة
- أفضل العملاء (أعلى إنفاق)
- المنتجات الأكثر مبيعاً
- معلومات مفصلة عن كل منتج وعميل

#### 4. تجربة مستخدم محسنة
- استجابات JSON منظمة ومفيدة
- رسائل نجاح وخطأ واضحة باللغة العربية
- معلومات إضافية مثل الفلاتر المطبقة والفترة الزمنية

### رموز الاستجابة

#### نجاح العمليات:
- `200` - تم بنجاح (عرض، فلترة، تقارير)

#### أخطاء:
- `401` - غير مصرح (لم يتم تسجيل الدخول)
- `403` - ممنوع (ليس لديك صلاحيات admin)
- `422` - خطأ في التحقق من البيانات
- `500` - خطأ في الخادم

### أمثلة على الأخطاء

#### خطأ في الصلاحيات (403):
```json
{
    "success": false,
    "message": "Access denied. Admin role required."
}
```

#### خطأ في التحقق من البيانات (422):
```json
{
    "success": false,
    "message": "فشل في التحقق من البيانات",
    "errors": {
        "type": ["The type field is required."],
        "year": ["The year must be between 2020 and 2030."]
    }
}
```

### سيناريو كامل: استخدام لوحة تحكم الأدمن

1. **عرض جميع الفواتير**
```bash
curl -X GET http://localhost:8000/admin/dashboard/invoices \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN"
```

2. **فلترة الفواتير المكتملة لهذا الشهر**
```bash
curl -X GET "http://localhost:8000/admin/dashboard/invoices/filter?status=completed&from=2024-01-01&to=2024-01-31" \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN"
```

3. **تقرير المبيعات الشهرية**
```bash
curl -X GET "http://localhost:8000/admin/dashboard/reports/sales?type=monthly&year=2024&month=1" \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN"
```

4. **تقرير المبيعات اليومية**
```bash
curl -X GET "http://localhost:8000/admin/dashboard/reports/sales?type=daily&date=2024-01-15" \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN"
```

### نصائح للاستخدام

1. **استخدم Pagination**: جميع endpoints تدعم pagination عبر المعامل `per_page`
2. **تحقق من الصلاحيات**: تأكد من أن المستخدم لديه صلاحيات admin
3. **استخدم التواريخ**: استخدم تنسيق التاريخ الصحيح (Y-m-d)
4. **احفظ التقارير**: التقارير تحتوي على معلومات قيمة لاتخاذ القرارات
5. **راقب الأداء**: استخدم التقارير لمراقبة أداء المبيعات والعملاء

## الدعم والمساعدة

إذا واجهت أي مشاكل أو تحتاج مساعدة، يمكنك:
1. مراجعة ملف DATABASE_DOCUMENTATION.md للتفاصيل التقنية
2. فحص logs في storage/logs/laravel.log
3. التأكد من تشغيل المايجريشن بشكل صحيح
