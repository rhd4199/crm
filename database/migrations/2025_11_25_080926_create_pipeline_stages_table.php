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
        Schema::create('pipeline_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')
                ->constrained()
                ->cascadeOnDelete();
    
            $table->string('name'); // Interest - Low, Contacted, Hold, Closed Won, dst
            $table->enum('type', ['open', 'hold', 'won', 'lost'])
                ->default('open'); // buat grouping di report
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_default')->default(false);
    
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pipeline_stages');
    }
};
