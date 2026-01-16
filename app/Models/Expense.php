<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Expense extends Model
{
    use HasUuids;

    protected $fillable = [
        'branch_id',
        'expense_date',
        'category',
        'description',
        'amount',
        'payment_method',
        'receipt_number',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
    ];

    // Categories
    public const CATEGORIES = [
        'rent' => 'ค่าเช่า',
        'utilities' => 'ค่าน้ำ/ค่าไฟ',
        'salary' => 'เงินเดือน/ค่าแรง',
        'supplies' => 'วัสดุสิ้นเปลือง',
        'maintenance' => 'ค่าซ่อมบำรุง',
        'marketing' => 'ค่าการตลาด',
        'equipment' => 'อุปกรณ์',
        'insurance' => 'ประกันภัย',
        'transport' => 'ค่าเดินทาง',
        'other' => 'อื่นๆ',
    ];

    // Payment methods
    public const PAYMENT_METHODS = [
        'cash' => 'เงินสด',
        'transfer' => 'โอนเงิน',
        'credit_card' => 'บัตรเครดิต',
        'cheque' => 'เช็ค',
    ];

    // Relationships
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Accessors
    public function getCategoryLabelAttribute()
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }

    public function getPaymentMethodLabelAttribute()
    {
        return self::PAYMENT_METHODS[$this->payment_method] ?? $this->payment_method;
    }

    // Scopes
    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('expense_date', $date);
    }

    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('expense_date', [$startDate, $endDate]);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}
