<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    use HasFactory;

    // 一括割り当てを許可するカラムを追加
    protected $fillable = [
        'name',
        'description',
        'lowest_price',
        'highest_price',
        'postal_code',
        'address',
        'opening_time',
        'closing_time',
        'seating_capacity',
        'category_ids',  // category_ids も追加
        'regular_holiday_ids',  // regular_holiday_ids も追加
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_restaurant');
    }

    public function regular_holidays() {
        return $this->belongsToMany(RegularHoliday::class, 'regular_holiday_restaurant', 'restaurant_id', 'regular_holiday_id');
    }
}
