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
        Schema::create('pipeline_stage_histories', function (Blueprint $table) {
            $table->id();
    
            $table->foreignId('customer_id')
                ->constrained()
                ->cascadeOnDelete();
    
            $table->foreignId('company_id')
                ->constrained()
                ->cascadeOnDelete();
    
            $table->foreignId('from_stage_id')
                ->nullable()
                ->constrained('pipeline_stages')
                ->nullOnDelete();
    
            $table->foreignId('to_stage_id')
                ->constrained('pipeline_stages')
                ->cascadeOnDelete();
    
            $table->foreignId('changed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
    
            $table->text('note')->nullable();
    
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pipeline_stage_histories');
    }
};
