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
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropColumn(['eye_side', 'sphere', 'cylinder', 'axis', 'h_prism', 'v_prism', 'add', 'intermediate_add']);
            $table->string('od_sphere')->nullable();
            $table->string('od_cylinder')->nullable();
            $table->string('od_axis')->nullable();
            $table->string('od_h_prism')->nullable();
            $table->string('od_v_prism')->nullable();
            $table->string('od_add')->nullable();
            $table->string('od_pd')->nullable();
            $table->string('od_fh')->nullable();
            
            $table->string('os_sphere')->nullable();
            $table->string('os_cylinder')->nullable();
            $table->string('os_axis')->nullable();
            $table->string('os_h_prism')->nullable();
            $table->string('os_v_prism')->nullable();
            $table->string('os_add')->nullable();
            $table->string('os_pd')->nullable();
            $table->string('os_fh')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropColumn([
                'od_sphere', 'od_cylinder', 'od_axis', 'od_h_prism', 'od_v_prism', 'od_add', 'od_pd', 'od_fh',
                'os_sphere', 'os_cylinder', 'os_axis', 'os_h_prism', 'os_v_prism', 'os_add', 'os_pd', 'os_fh'
            ]);
            $table->string('eye_side')->default('Both');
            $table->string('sphere')->nullable();
            $table->string('cylinder')->nullable();
            $table->string('axis')->nullable();
            $table->string('h_prism')->nullable();
            $table->string('v_prism')->nullable();
            $table->string('add')->nullable();
            $table->string('intermediate_add')->nullable();
        });
    }
};
