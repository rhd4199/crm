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
        Schema::create('interactions', function (Blueprint $table) {
            $table->id();
    
            $table->foreignId('customer_id')
                ->constrained()
                ->cascadeOnDelete();
    
            $table->foreignId('company_id')
                ->constrained()
                ->cascadeOnDelete();
    
            $table->foreignId('user_id') // marketing / CS yang melakukan
                ->constrained()
                ->cascadeOnDelete();
    
            $table->enum('channel', ['whatsapp', 'phone', 'email', 'meeting', 'other'])
                ->default('other');
    
            $table->enum('direction', ['outbound', 'inbound'])
                ->default('outbound');
    
            $table->string('subject')->nullable(); // optional
            $table->text('summary')->nullable();   // isi follow up
    
            $table->dateTime('follow_up_at')->nullable(); // jadwal follow up berikutnya
            $table->integer('duration_seconds')->nullable(); // kalau telpon
    
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interactions');
    }
};
