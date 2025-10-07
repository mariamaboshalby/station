<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shift extends Model
{
    protected $fillable = [
        'user_id',
        'operation_type',
        'meter_reading',      // قراءة العداد عند الفتح
        'meter_match',        // مطابقة العداد
        'meter_image',        // صورة العداد عند الفتح
        'cash_sales',         // مبيعات نقدية
        'credit_sales',       // مبيعات آجلة
        'end_meter_reading',  // قراءة نهاية الشيفت
        'end_meter_image',    // صورة نهاية الشيفت
        'notes',              // ملاحظات عند الإغلاق
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time'   => 'datetime',
    ];

    // العلاقة مع المستخدم
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // العلاقة مع العمليات
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // 🔹 إجمالي المبيعات النقدية
    public function getTotalCashAttribute()
    {
        return $this->transactions->sum('cash_amount');
    }

    // 🔹 إجمالي المبيعات الآجلة
    public function getTotalCreditAttribute()
    {
        return $this->transactions->sum('credit_amount');
    }

    // 🔹 إجمالي المبيعات الكلية
    public function getTotalSalesAttribute()
    {
        return $this->total_cash + $this->total_credit;
    }

    // 🔹 عرض وقت البداية بتوقيت القاهرة
    public function getStartTimeCairoAttribute()
    {
        return $this->start_time
            ? $this->start_time->setTimezone('Africa/Cairo')->format('Y-m-d H:i:s')
            : null;
    }

    // 🔹 عرض وقت النهاية بتوقيت القاهرة
    public function getEndTimeCairoAttribute()
    {
        return $this->end_time
            ? $this->end_time->setTimezone('Africa/Cairo')->format('Y-m-d H:i:s')
            : null;
    }
}
