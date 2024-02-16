<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
class Invoice extends Model
{
    use HasFactory, SoftDeletes;
    protected  $fillable =['invoice_number','invoice_date','due_date','discount','rate_vat','value_vat','total',
    'amout_commission','amount_collection','status','value_status','note','user_id','product_id','category_id','deleted_at'];
    public function product(): BelongsTo

    {
        return $this->belongsTo(Product::class);
    }
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function attachments()
    {
        return $this->hasMany(InvoiceAttachment::class);
    }

    public function details()
    {
        return $this->hasMany(InvoiceDetails::class);
    }
    public function calculateValue($amout_commission, $discount, $rate_vat){
        return (($amout_commission - $discount) * ($rate_vat / 100));
    }

    public function calculateTotal($amout_commission, $discount, $rate_vat){
        return ($amout_commission - $discount) + $this->calculateValue($amout_commission, $discount, $rate_vat);
    }

}
