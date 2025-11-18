<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('threads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('board_id')->constrained()->onDelete('cascade');
            $table->string('subject', 100);
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->integer('reply_count')->default(0);
            $table->integer('image_count')->default(1); // OP always has image
            $table->timestamp('last_bump_at')->useCurrent();
            $table->timestamps();
            $table->softDeletes(); // For auto-archiving

            $table->index(['board_id', 'is_pinned', 'last_bump_at']);
            $table->index('last_bump_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('threads');
    }
};
