<?php

use App\Enums\TransactionStatusEnum;
use App\Enums\TransactionTypeEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignId('card_id')->constrained('cards');
            $table->enum('type', TransactionTypeEnum::values())->index();
            $table->enum('status', TransactionStatusEnum::values())
                ->default(TransactionStatusEnum::INIT->value)->index();
            $table->decimal('amount', 13, 0);
            $table->string('track_id')->nullable();
            $table->timestamp('done_at')->nullable();
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
