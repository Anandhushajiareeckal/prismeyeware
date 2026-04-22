<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repair_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('repair_id')->constrained()->cascadeOnDelete();
            $table->string('repair_type');
            $table->decimal('price', 10, 2)->default(0);
            $table->timestamps();
        });

        // Migrate old data securely
        $repairs = DB::table('repairs')->whereNotNull('repair_type')->get();
        foreach($repairs as $r) {
            DB::table('repair_items')->insert([
                'repair_id' => $r->id,
                'repair_type' => $r->repair_type,
                'price' => $r->repair_price,
            ]);
        }

        Schema::table('repairs', function (Blueprint $table) {
            $table->dropColumn('repair_type');
        });
    }

    public function down(): void
    {
        Schema::table('repairs', function (Blueprint $table) {
            $table->string('repair_type')->nullable();
        });

        $items = DB::table('repair_items')->get();
        foreach($items as $item) {
            DB::table('repairs')->where('id', $item->repair_id)->update([
                'repair_type' => $item->repair_type,
                'repair_price' => $item->price
            ]);
        }
        
        Schema::dropIfExists('repair_items');
    }
};
