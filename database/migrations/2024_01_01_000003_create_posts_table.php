<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thread_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('post_number'); // Unique per board
            $table->string('name', 100)->default('Anonymous');
            $table->text('content');
            $table->string('image_path')->nullable();
            $table->string('image_thumbnail_path')->nullable();
            $table->string('ip_address_hash', 64); // SHA-256 hash
            $table->timestamps();

            // Composite unique index for post_number per board
            // We'll need to query through thread to get board_id
            $table->index(['thread_id', 'created_at']);
            $table->index('post_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
