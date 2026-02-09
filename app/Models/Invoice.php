<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory;

    protected $table = 'orders'; // Using existing orders table
    protected $fillable = [
        'invoice_no',
        'invoice_date',
        'invoice_generated',
        'order_no',
        'order_date',
        'user_id',
        'sub_total',
        'discount',
        'delivery_fee',
        'vat',
        'tax',
        'total',
        'order_note'
    ];

    // Relationship with order details
    public function invoiceDetails()
    {
        return $this->hasMany(OrderDetails::class, 'order_id', 'id');
    }

    // Relationship with customer/user
    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relationship with shipping info
    public function shippingInfo()
    {
        return $this->hasOne(ShippingInfo::class, 'order_id', 'id');
    }

    // Relationship with billing address
    public function billingAddress()
    {
        return $this->hasOne(BillingAddress::class, 'order_id', 'id');
    }

    // Generate invoice number
    public static function generateInvoiceNumber($orderId)
    {
        $today = date('ymd');
        $count = self::where('invoice_date', 'LIKE', date('Y-m-d') . '%')
                    ->where('invoice_generated', 1)
                    ->count();
        
        return 'INV-' . $today . '-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    }

    // Check if order has invoice
    public static function hasInvoice($orderId)
    {
        return self::where('id', $orderId)->where('invoice_generated', 1)->exists();
    }

    // Mark order as invoiced
    public function markAsInvoiced()
    {
        $this->invoice_no = self::generateInvoiceNumber($this->id);
        $this->invoice_date = now();
        $this->invoice_generated = 1;
        $this->save();
        
        return $this;
    }
}
