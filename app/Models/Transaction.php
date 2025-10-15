<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Transaction extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'shift_id',
        'pump_id',
        'client_id',
        'credit_liters',
        'cash_liters',
        'total_amount',
        'notes',
    ];

    /**
     * 🔄 إعداد تحويلات الصور (مثل النسخ المصغّرة)
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300)
            ->quality(75)
            ->nonQueued(); // ينفذ التحويل فوراً بدون كيو
    }

    /**
     * 💾 العلاقات
     */
    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function pump()
    {
        return $this->belongsTo(Pump::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
