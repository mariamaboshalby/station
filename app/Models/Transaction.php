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
        'invoice_number',
        'batch_number',
        'shift_id',
        'pump_id',
        'nozzle_id',
        'client_id',
        'vehicle_number',
        'credit_liters',
        'cash_liters',
        'total_amount',
        'notes',
    ];

    public function registerMediaConversions(Media $media = null): void
    {
        // تعطيل التحويلات مؤقتاً
    }

    /**
     * 💾 العلاقات
     */
    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function nozzle()
    {
        return $this->belongsTo(Nozzle::class);
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
