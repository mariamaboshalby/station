<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'shift_id',
        'user_id',
        'category',
        'amount',
        'description',
        'expense_date',
        'invoice_number',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
    ];

    // العلاقة مع الموظف
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // العلاقة مع الشيفت
    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    // دالة مساعدة لترجمة الفئات للعربية
    public function getCategoryLabelAttribute()
    {
        return match($this->category) {
            'operational' => 'تشغيل',
            'labor' => 'عمالة',
            'maintenance' => 'صيانة',
            'purchasing' => 'مشتريات',
            'utilities' => 'مرافق',
            'other' => 'أخرى',
            default => $this->category,
        };
    }
}
