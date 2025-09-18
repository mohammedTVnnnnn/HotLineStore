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

#### البحث في المنتجات
```bash
curl -X GET "http://localhost:8000/api/products/search?query=لابتوب"
```

#### فلترة المنتجات حسب السعر
```bash
curl -X GET "http://localhost:8000/api/products/price-range?min_price=1000&max_price=3000"
```

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
# منتج 1
curl -X POST http://localhost:8000/api/products \
  -H "Content-Type: application/json" \
  -d '{
    "name": "هاتف آيفون",
    "description": "هاتف ذكي حديث",
    "price": 3000.00,
    "stock": 5
  }'

# منتج 2
curl -X POST http://localhost:8000/api/products \
  -H "Content-Type: application/json" \
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

## الدعم والمساعدة

إذا واجهت أي مشاكل أو تحتاج مساعدة، يمكنك:
1. مراجعة ملف DATABASE_DOCUMENTATION.md للتفاصيل التقنية
2. فحص logs في storage/logs/laravel.log
3. التأكد من تشغيل المايجريشن بشكل صحيح
