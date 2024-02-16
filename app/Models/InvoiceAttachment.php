<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceAttachment extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [

        'file_path',
        'invoice_id',
        // Add other fillable fields as needed
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
