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
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->date('prescription_date');
            $table->date('recall_date')->nullable();
            $table->string('type')->default('Spectacle'); 
            $table->string('doctor_name')->nullable();
            $table->string('eye_side');
            $table->string('sphere')->nullable();
            $table->string('cylinder')->nullable();
            $table->string('axis')->nullable();
            $table->string('h_prism')->nullable();
            $table->string('v_prism')->nullable();
            $table->string('add')->nullable();
            $table->string('intermediate_add')->nullable();
            $table->text('comments')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
