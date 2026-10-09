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
        Schema::create('v_card_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('v_card_id')->constrained()->cascadeOnDelete();
            $table->string('type', 40);
            $table->string('label');
            $table->string('url', 2048);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['v_card_id', 'type']);
            $table->index(['v_card_id', 'is_active', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('v_card_links');
    }
};
