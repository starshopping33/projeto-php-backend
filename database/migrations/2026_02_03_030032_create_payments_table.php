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
        if (! Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('subscription_id');
            
            $table->string('provider', 50)->default('mercado_pago');
            $table->string('provider_payment_id')->nullable();

            $table->decimal('amount', 10, 2)->notNullable();
            $table->string('currency', 10)->default('BRL');

            $table->string('payment_method', 50)->notNullable();
            $table->enum('status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->dateTime('paid_at')->nullable();
            $table->json('raw_response')->nullable();
            $table->timestamps();
        });
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
