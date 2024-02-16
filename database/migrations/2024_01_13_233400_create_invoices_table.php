<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->require()->unique();
            $table->date('invoice_date')->require();
            $table->date('due_date')->require();
            $table->decimal('discount',  8, 2)->require();
            $table->decimal('rate_vat',  8, 2)->require();
            $table->decimal('value_vat', 8, 2)->require();
            $table->decimal('total',  8, 2)->require();
            $table->decimal('amount_collection',  8, 2)->require();
            $table->decimal('amout_commission',  8, 2)->require();
            $table->string('status')->require();
            $table->integer('value_status')->require();
            $table->text('note')->require();
            $table->foreignId('product_id')->constrained('products')->require()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->require()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->require()->cascadeOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
