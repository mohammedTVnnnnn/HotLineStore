<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء التصنيفات الرئيسية
        $electronics = Category::create([
            'name' => 'إلكترونيات',
            'description' => 'جميع الأجهزة الإلكترونية والتقنية',
            'slug' => 'electronics',
            'is_active' => true,
            'sort_order' => 1
        ]);

        $clothing = Category::create([
            'name' => 'ملابس',
            'description' => 'ملابس رجالية ونسائية وأطفال',
            'slug' => 'clothing',
            'is_active' => true,
            'sort_order' => 2
        ]);

        $home = Category::create([
            'name' => 'منزل ومطبخ',
            'description' => 'أدوات منزلية ومطبخية',
            'slug' => 'home-kitchen',
            'is_active' => true,
            'sort_order' => 3
        ]);

        $books = Category::create([
            'name' => 'كتب ومجلات',
            'description' => 'كتب ومجلات ومواد تعليمية',
            'slug' => 'books-magazines',
            'is_active' => true,
            'sort_order' => 4
        ]);

        // إنشاء التصنيفات الفرعية للإلكترونيات
        $smartphones = Category::create([
            'name' => 'هواتف ذكية',
            'description' => 'هواتف ذكية حديثة',
            'slug' => 'smartphones',
            'parent_id' => $electronics->id,
            'is_active' => true,
            'sort_order' => 1
        ]);

        $laptops = Category::create([
            'name' => 'لابتوبات',
            'description' => 'لابتوبات وأجهزة كمبيوتر محمولة',
            'slug' => 'laptops',
            'parent_id' => $electronics->id,
            'is_active' => true,
            'sort_order' => 2
        ]);

        $accessories = Category::create([
            'name' => 'إكسسوارات إلكترونية',
            'description' => 'إكسسوارات للهواتف والأجهزة الإلكترونية',
            'slug' => 'electronics-accessories',
            'parent_id' => $electronics->id,
            'is_active' => true,
            'sort_order' => 3
        ]);

        // إنشاء التصنيفات الفرعية للملابس
        $mensClothing = Category::create([
            'name' => 'ملابس رجالية',
            'description' => 'ملابس رجالية عصرية',
            'slug' => 'mens-clothing',
            'parent_id' => $clothing->id,
            'is_active' => true,
            'sort_order' => 1
        ]);

        $womensClothing = Category::create([
            'name' => 'ملابس نسائية',
            'description' => 'ملابس نسائية أنيقة',
            'slug' => 'womens-clothing',
            'parent_id' => $clothing->id,
            'is_active' => true,
            'sort_order' => 2
        ]);

        $kidsClothing = Category::create([
            'name' => 'ملابس أطفال',
            'description' => 'ملابس أطفال مريحة وجميلة',
            'slug' => 'kids-clothing',
            'parent_id' => $clothing->id,
            'is_active' => true,
            'sort_order' => 3
        ]);

        // إنشاء التصنيفات الفرعية للمنزل
        $furniture = Category::create([
            'name' => 'أثاث',
            'description' => 'أثاث منزلي ومكتبي',
            'slug' => 'furniture',
            'parent_id' => $home->id,
            'is_active' => true,
            'sort_order' => 1
        ]);

        $kitchenTools = Category::create([
            'name' => 'أدوات مطبخ',
            'description' => 'أدوات ومعدات مطبخية',
            'slug' => 'kitchen-tools',
            'parent_id' => $home->id,
            'is_active' => true,
            'sort_order' => 2
        ]);

        // إنشاء تصنيفات فرعية للهواتف الذكية
        $iphone = Category::create([
            'name' => 'آيفون',
            'description' => 'هواتف آيفون من آبل',
            'slug' => 'iphone',
            'parent_id' => $smartphones->id,
            'is_active' => true,
            'sort_order' => 1
        ]);

        $samsung = Category::create([
            'name' => 'سامسونج',
            'description' => 'هواتف سامسونج الذكية',
            'slug' => 'samsung',
            'parent_id' => $smartphones->id,
            'is_active' => true,
            'sort_order' => 2
        ]);

        $huawei = Category::create([
            'name' => 'هواوي',
            'description' => 'هواتف هواوي الذكية',
            'slug' => 'huawei',
            'parent_id' => $smartphones->id,
            'is_active' => true,
            'sort_order' => 3
        ]);

        $this->command->info('تم إنشاء ' . Category::count() . ' تصنيف بنجاح!');
    }
}
