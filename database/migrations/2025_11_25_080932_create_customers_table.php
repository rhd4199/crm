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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
    
            $table->foreignId('company_id')
                ->constrained()
                ->cascadeOnDelete();
    
            $table->string('name');
            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable();
            $table->string('source')->nullable(); // IG, WA, Referral, dsb
            $table->string('tag')->nullable();    // bebas, kalau perlu
    
            // marketing & CS yang handle (optional)
            $table->foreignId('assigned_marketing_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
    
            $table->foreignId('assigned_cs_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
    
            // stage sekarang
            $table->foreignId('current_stage_id')
                ->nullable()
                ->constrained('pipeline_stages')
                ->nullOnDelete();
    
            // siapa yang input
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
    
            $table->text('notes')->nullable();
    
            // nilai deal (kalau mau)
            $table->decimal('estimated_value', 15, 2)->nullable();
    
            $table->timestamp('last_contact_at')->nullable();
    
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
