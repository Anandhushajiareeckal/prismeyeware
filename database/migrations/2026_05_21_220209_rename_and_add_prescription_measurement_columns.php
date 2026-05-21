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
            $table->renameColumn('od_add', 'od_n_add');
            $table->renameColumn('os_add', 'os_n_add');
        });

        Schema::table('prescriptions', function (Blueprint $table) {
            $table->string('od_i_add')->nullable()->after('od_n_add');
            $table->string('os_i_add')->nullable()->after('os_n_add');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropColumn(['od_i_add', 'os_i_add']);
        });

        Schema::table('prescriptions', function (Blueprint $table) {
            $table->renameColumn('od_n_add', 'od_add');
            $table->renameColumn('os_n_add', 'os_add');
        });
    }
};
