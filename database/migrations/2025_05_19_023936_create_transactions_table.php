<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['deposit', 'transfer', 'reversal']);
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('recipient_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->text('description')->nullable();
            $table->foreignId('reversed_by')->nullable()->constrained('transactions')->onDelete('set null');
            $table->foreignId('original_transaction')->nullable()->constrained('transactions')->onDelete('set null');
            $table->boolean('is_reversed')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
