<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceDetails extends Model
{
    use HasFactory;
    protected  $fillable =['status','value_status','payment_date','invoice_id','user_id'];
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
