<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('fees', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignId('transaction_id')->constrained('transactions');
            $table->decimal('amount', 13, 0)->default(0);
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('fees');
    }
};
