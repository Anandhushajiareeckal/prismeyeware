<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repair_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        $uniqueTypes = DB::table('repair_items')
            ->select('repair_type')
            ->distinct()
            ->whereNotNull('repair_type')
            ->pluck('repair_type');

        foreach($uniqueTypes as $type) {
            DB::table('repair_types')->insertOrIgnore([
                'name' => $type,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('repair_types');
    }
};
