<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use HasFactory;

    protected $table = 'medicines';

    protected $fillable = [
    'name',
    'price',
    'stock',
    'image',
    'description',
    'images',
    'category_id',
    'age_restriction',
    'expiry_date'
];

    protected $casts = [
        'images' => 'json'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

}
