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
        Schema::table('repair_types', function (Blueprint $table) {
            $table->decimal('delivery_charge', 10, 2)->nullable()->after('name');
        });

        Schema::table('repairs', function (Blueprint $table) {
            $table->decimal('delivery_charge', 10, 2)->nullable()->after('repair_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('repair_types', function (Blueprint $table) {
            $table->dropColumn('delivery_charge');
        });

        Schema::table('repairs', function (Blueprint $table) {
            $table->dropColumn('delivery_charge');
        });
    }
};
