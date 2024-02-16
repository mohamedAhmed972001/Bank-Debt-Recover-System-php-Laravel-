<?php

namespace App\Models;
use App\Models\Category;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Product extends Model
{
    use HasFactory;
    protected  $fillable =['name','description','category_id'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    public function invoices(){
        return $this->hasMany(Invoice::class,'product_id');
    }

}
