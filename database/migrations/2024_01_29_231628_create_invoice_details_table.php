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
        Schema::create('invoice_details', function (Blueprint $table) {
            $table->id();
            $table->string('status')->require();
            $table->integer('value_status')->require();
            $table->date('payment_date')->require();
            $table->foreignId('invoice_id')->constrained('invoices')->require()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->require()->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_details');
    }
};
