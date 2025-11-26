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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('company_id')
                ->nullable()
                ->after('id')
                ->constrained()
                ->nullOnDelete();
    
            // super_admin = penyedia layanan, user = user biasa (perusahaan)
            $table->enum('global_role', ['super_admin', 'user'])
                ->default('user')
                ->after('company_id');
    
            // role di level perusahaan
            $table->enum('company_role', ['admin', 'marketing', 'customer_service'])
                ->nullable()
                ->after('global_role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn(['company_id', 'global_role', 'company_role']);
        });
    }
};
