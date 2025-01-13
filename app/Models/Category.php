<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    /**
     * マスアサイン可能な属性のリスト
     *
     * @var array
     */
    protected $fillable = [
        'name', // マスアサイン可能なカラムをリストアップ
    ];

    public function restaurants()
    {
        return $this->belongsToMany(Restaurant::class, 'category_restaurant');
    }
}
