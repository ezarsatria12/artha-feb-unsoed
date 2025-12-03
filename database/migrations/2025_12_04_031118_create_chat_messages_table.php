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
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            // Menyimpan ID user yang melakukan chat
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Pengirim: 'user' atau 'admin'
            $table->enum('sender', ['user', 'admin'])->default('user');
            
            // Isi pesan
            $table->text('message');
            
            // Status baca (opsional)
            $table->boolean('is_read')->default(false);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
