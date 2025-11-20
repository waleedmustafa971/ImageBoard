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
        Schema::create('bans', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45); // IPv4 or IPv6
            $table->foreignId('board_id')->nullable()->constrained()->onDelete('cascade'); // null = global ban
            $table->text('reason');
            $table->string('banned_by_type'); // Polymorphic: Admin or Supervisor
            $table->unsignedBigInteger('banned_by_id');
            $table->timestamp('expires_at')->nullable(); // null = permanent ban
            $table->timestamps();

            $table->index(['ip_address', 'board_id', 'expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bans');
    }
};
