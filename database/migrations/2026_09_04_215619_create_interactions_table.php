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
            $table->foreignId('v_card_id')->constrained()->cascadeOnDelete();
            $table->foreignId('qr_code_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('v_card_link_id')->nullable()->constrained()->nullOnDelete();
            $table->string('event_type', 40);
            $table->string('country', 100)->nullable();
            $table->string('city', 120)->nullable();
            $table->string('device', 40)->nullable();
            $table->string('browser', 40)->nullable();
            $table->char('ip_hash', 64)->nullable();
            $table->timestamp('occurred_at')->useCurrent();

            $table->index(['v_card_id', 'event_type', 'occurred_at']);
            $table->index(['v_card_id', 'occurred_at']);
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
