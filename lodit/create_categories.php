<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Category;

// Create parent categories
$painRelief = Category::create(['name' => 'Pain Relief', 'slug' => 'pain-relief', 'description' => 'Medicine for various types of pain']);
$coldFlu = Category::create(['name' => 'Cold & Flu', 'slug' => 'cold-flu', 'description' => 'Medicine for cold and flu symptoms']);
$digestion = Category::create(['name' => 'Digestion', 'slug' => 'digestion', 'description' => 'Medicine for digestive health']);
$vitamins = Category::create(['name' => 'Vitamins & Supplements', 'slug' => 'vitamins-supplements', 'description' => 'Vitamins and nutritional supplements']);
$skin = Category::create(['name' => 'Skin Care', 'slug' => 'skin-care', 'description' => 'Skincare and dermatological products']);

// Create subcategories for Pain Relief
Category::create(['name' => 'Headache Relief', 'slug' => 'headache-relief', 'parent_id' => $painRelief->id, 'description' => 'Medication for headaches and migraines']);
Category::create(['name' => 'Body Pain', 'slug' => 'body-pain', 'parent_id' => $painRelief->id, 'description' => 'Medicine for muscle and joint pain']);
Category::create(['name' => 'Fever Reducer', 'slug' => 'fever-reducer', 'parent_id' => $painRelief->id, 'description' => 'Fever reducing medication']);

// Create subcategories for Cold & Flu
Category::create(['name' => 'Cough Medicine', 'slug' => 'cough-medicine', 'parent_id' => $coldFlu->id, 'description' => 'Cough suppressants and expectorants']);
Category::create(['name' => 'Nasal Congestion', 'slug' => 'nasal-congestion', 'parent_id' => $coldFlu->id, 'description' => 'Decongestants for nasal relief']);
Category::create(['name' => 'Sore Throat', 'slug' => 'sore-throat', 'parent_id' => $coldFlu->id, 'description' => 'Medicine for throat pain and irritation']);

// Create subcategories for Digestion
Category::create(['name' => 'Antacids', 'slug' => 'antacids', 'parent_id' => $digestion->id, 'description' => 'Antacid products for acid reflux']);
Category::create(['name' => 'Laxatives', 'slug' => 'laxatives', 'parent_id' => $digestion->id, 'description' => 'Digestive aids and laxatives']);
Category::create(['name' => 'Probiotics', 'slug' => 'probiotics', 'parent_id' => $digestion->id, 'description' => 'Beneficial bacteria for gut health']);

// Create subcategories for Vitamins & Supplements
Category::create(['name' => 'Multivitamins', 'slug' => 'multivitamins', 'parent_id' => $vitamins->id, 'description' => 'Complete multivitamin supplements']);
Category::create(['name' => 'Vitamin C', 'slug' => 'vitamin-c', 'parent_id' => $vitamins->id, 'description' => 'Vitamin C supplements']);
Category::create(['name' => 'Calcium & Minerals', 'slug' => 'calcium-minerals', 'parent_id' => $vitamins->id, 'description' => 'Calcium and mineral supplements']);

// Create subcategories for Skin Care
Category::create(['name' => 'Acne Treatment', 'slug' => 'acne-treatment', 'parent_id' => $skin->id, 'description' => 'Products for acne and pimple treatment']);
Category::create(['name' => 'Moisturizers', 'slug' => 'moisturizers', 'parent_id' => $skin->id, 'description' => 'Skin moisturizers and lotions']);
Category::create(['name' => 'Anti-aging', 'slug' => 'anti-aging', 'parent_id' => $skin->id, 'description' => 'Anti-aging skin products']);

echo "Categories created successfully!\n";
