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
        Schema::create('repairs', function (Blueprint $table) {
            $table->id();
            $table->string('repair_number')->unique();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('sku')->nullable();
            $table->date('repair_date');
            $table->string('repair_type')->nullable();
            $table->text('repair_notes')->nullable();
            $table->text('collection_notes')->nullable();
            $table->string('assigned_staff')->nullable();
            $table->decimal('repair_price', 10, 2)->default(0);
            $table->string('status')->default('Pending');
            $table->date('completion_date')->nullable();
            $table->date('collected_date')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repairs');
    }
};
