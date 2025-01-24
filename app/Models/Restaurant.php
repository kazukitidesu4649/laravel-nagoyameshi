<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Restaurant extends Model
{
    use HasFactory, Sortable;

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

    // sortableに割り当て
    public $sortable = [
        'created_at',
        'updated_at'
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_restaurant');
    }

    public function regular_holidays() {
        return $this->belongsToMany(RegularHoliday::class, 'regular_holiday_restaurant', 'restaurant_id', 'regular_holiday_id');
    }

    /**
     * 評価順で並び替える
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRatingSortable($query)
    {
        return $query->orderBy('rating', 'desc');
    }

    /**
     * 人気順で並び替える（例: 予約数で並び替える）
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePopularSortable($query)
    {
        return $query->orderBy('popular', 'desc'); // popularが予約数と仮定
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // 店舗の平均評価順
    public static function ratingSortable($query)
    {
        return $query->withAvg('reviews', 'score')->orderBy('reviews_avg_score', 'desc');
    }

    public function reservations() {
        return $this->hasMany(Reservation::class);
    }
}
