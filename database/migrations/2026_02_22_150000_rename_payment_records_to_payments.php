<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('payment_records') && ! Schema::hasTable('payments')) {
            Schema::rename('payment_records', 'payments');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('payments') && ! Schema::hasTable('payment_records')) {
            Schema::rename('payments', 'payment_records');
        }
    }
};
