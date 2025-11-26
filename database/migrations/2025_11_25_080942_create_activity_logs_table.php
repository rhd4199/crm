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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
    
            $table->foreignId('company_id')
                ->constrained()
                ->cascadeOnDelete();
    
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
    
            // contoh action: customer_created, customer_deleted, stage_changed
            $table->string('action');
    
            // polymorphic: bisa log ke customer / pipeline history / dll
            $table->morphs('loggable'); // loggable_id, loggable_type
    
            $table->json('data')->nullable(); // detail sebelum/sesudah, dsb
    
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
