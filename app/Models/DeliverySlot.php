<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliverySlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'date',
        'start_time',
        'end_time',
        'type',
        'max_orders',
        'current_orders',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'is_active' => 'boolean',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_active', true)
            ->whereRaw('current_orders < max_orders')
            ->where('date', '>=', now()->toDateString());
    }

    public function scopeDelivery($query)
    {
        return $query->where('type', 'delivery');
    }

    public function scopeCollection($query)
    {
        return $query->where('type', 'collection');
    }

    public function isAvailable(): bool
    {
        return $this->is_active 
            && $this->current_orders < $this->max_orders 
            && $this->date >= now()->toDateString();
    }

    public function isFull(): bool
    {
        return $this->current_orders >= $this->max_orders;
    }

    public function getTimeRangeAttribute(): string
    {
        return date('H:i', strtotime($this->start_time)) . ' - ' . date('H:i', strtotime($this->end_time));
    }
}
