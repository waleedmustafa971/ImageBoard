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
        Schema::create('moderation_logs', function (Blueprint $table) {
            $table->id();
            $table->string('moderator_type'); // 'admin' or 'supervisor'
            $table->unsignedBigInteger('moderator_id');
            $table->string('action'); // 'delete_thread', 'delete_post', 'pin_thread', 'lock_thread', etc.
            $table->string('target_type'); // 'thread', 'post', 'board'
            $table->unsignedBigInteger('target_id');
            $table->foreignId('board_id')->nullable()->constrained()->onDelete('set null');
            $table->text('details')->nullable(); // JSON or additional context
            $table->timestamps();

            // Index for querying logs
            $table->index(['moderator_type', 'moderator_id']);
            $table->index(['board_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moderation_logs');
    }
};
