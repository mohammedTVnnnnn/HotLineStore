# تدفق عملية Checkout - نظام الفواتير

## نظرة عامة
يوضح هذا الملف تدفق عملية Checkout من السلة إلى الفاتورة في نظام HotLine للتجارة الإلكترونية.

## مخطط تدفق العملية

```
المستخدم المصادق عليه
         ↓
    POST /api/checkout
         ↓
InvoiceController::checkout()
         ↓
InvoiceService::createInvoiceFromUserCart()
         ↓
┌─────────────────────────────────────┐
│     Database Transaction Start      │
└─────────────────────────────────────┘
         ↓
1. جلب السلة النشطة للمستخدم
         ↓
2. التحقق من وجود منتجات في السلة
         ↓
3. التحقق من توفر المخزون
         ↓
4. حساب الإجمالي
         ↓
5. إنشاء فاتورة جديدة
         ↓
6. نسخ المنتجات إلى invoice_items
         ↓
7. تقليل المخزون في المنتجات
         ↓
8. تفريغ السلة
         ↓
┌─────────────────────────────────────┐
│      Database Transaction End       │
└─────────────────────────────────────┘
         ↓
    إرجاع الفاتورة
         ↓
    Response JSON (201 Created)
```

## خطوات العملية التفصيلية

### 1. التحقق من المصادقة
- التحقق من وجود مستخدم مصادق عليه
- إذا لم يكن مصادق: إرجاع 401 Unauthorized

### 2. Validation
- التحقق من صحة البيانات المرسلة
- التحقق من حالة الفاتورة (pending, completed, cancelled)

### 3. جلب السلة النشطة
```php
$cart = Cart::with(['cartItems.product'])
    ->where('user_id', $userId)
    ->latest()
    ->first();
```

### 4. التحقق من السلة
- التحقق من وجود سلة نشطة
- التحقق من وجود منتجات في السلة
- إذا كانت فارغة: إرجاع 400 Bad Request

### 5. التحقق من المخزون
```php
foreach ($cart->cartItems as $item) {
    if ($item->product->stock < $item->quantity) {
        throw new \Exception("الكمية المطلوبة غير متوفرة");
    }
}
```

### 6. حساب الإجمالي
```php
$total = 0;
foreach ($cart->cartItems as $item) {
    $total += $item->quantity * $item->product->price;
}
```

### 7. إنشاء الفاتورة
```php
$invoice = Invoice::create([
    'user_id' => $userId,
    'total' => round($total, 2),
    'status' => $status
]);
```

### 8. نسخ المنتجات وتحديث المخزون
```php
foreach ($cart->cartItems as $cartItem) {
    // إنشاء عنصر الفاتورة
    InvoiceItem::create([
        'invoice_id' => $invoice->id,
        'product_id' => $cartItem->product_id,
        'quantity' => $cartItem->quantity,
        'price' => $cartItem->product->price
    ]);

    // تقليل المخزون
    $cartItem->product->decrement('stock', $cartItem->quantity);
}
```

### 9. تفريغ السلة
```php
$cart->cartItems()->delete();
```

### 10. إرجاع النتيجة
- إرجاع الفاتورة مع تفاصيل المنتجات
- Response Code: 201 Created

## معالجة الأخطاء

### أخطاء محتملة:
1. **401 Unauthorized**: المستخدم غير مصادق
2. **404 Not Found**: لا توجد سلة نشطة للمستخدم
3. **400 Bad Request**: السلة فارغة أو المخزون غير كافي
4. **422 Unprocessable Entity**: خطأ في validation

### رسائل الخطأ:
- جميع الرسائل باللغة العربية
- تفاصيل واضحة عن سبب الخطأ
- إرشادات لحل المشكلة

## الميزات الأمان

### Database Transactions
- جميع العمليات داخل Transaction واحد
- في حالة حدوث خطأ، يتم Rollback جميع التغييرات
- ضمان ACID properties

### التحقق من الملكية
- المستخدم يمكنه الوصول فقط لفواتيره
- التحقق من ملكية السلة قبل إنشاء الفاتورة

### حماية البيانات
- جميع الـ endpoints محمية بـ auth:sanctum
- التحقق من صحة البيانات في كل خطوة

## مثال على الاستخدام

### Request
```bash
curl -X POST http://localhost:8000/api/checkout \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{"status": "completed"}'
```

### Response Success
```json
{
    "success": true,
    "data": {
        "id": 1,
        "user_id": 1,
        "total": "8400.00",
        "status": "completed",
        "invoice_items": [
            {
                "id": 1,
                "product_id": 1,
                "quantity": 2,
                "price": "4000.00",
                "product": {
                    "name": "هاتف آيفون 15",
                    "price": "4000.00"
                }
            }
        ]
    },
    "message": "تم إنشاء الفاتورة بنجاح من السلة"
}
```

### Response Error
```json
{
    "success": false,
    "message": "السلة فارغة، لا يمكن إنشاء فاتورة"
}
```

## النتائج المتوقعة

### بعد عملية Checkout الناجحة:
1. ✅ فاتورة جديدة تم إنشاؤها
2. ✅ المنتجات نسخت إلى invoice_items
3. ✅ المخزون قل في جدول المنتجات
4. ✅ السلة فرغت من المحتويات
5. ✅ المستخدم يمكنه عرض الفاتورة في قائمة فواتيره

### البيانات المحفوظة:
- **الفاتورة**: رقم الفاتورة، المستخدم، الإجمالي، الحالة، التاريخ
- **عناصر الفاتورة**: المنتج، الكمية، السعر وقت الشراء
- **تاريخ الأسعار**: حتى لو تغير سعر المنتج لاحقاً

هذا النظام يوفر تجربة مستخدم سلسة وآمنة مع ضمان سلامة البيانات في جميع المراحل.
