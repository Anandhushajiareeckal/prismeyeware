<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('prescription_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('status')->default('Active');
            $table->timestamps();
        });

        // Insert default types securely
        DB::table('prescription_types')->insert([
            ['name' => 'Distance', 'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Reading', 'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bifocal', 'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Progressive', 'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Contact Lens', 'status' => 'Active', 'created_at' => now(), 'updated_at' => now()]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescription_types');
    }
};
