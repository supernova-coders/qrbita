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
        Schema::create('v_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('v_card_template_id')->nullable();
            $table->string('slug', 100)->unique();
            $table->string('title');
            $table->string('public_name');
            $table->string('organization')->nullable();
            $table->string('job_title')->nullable();
            $table->string('phone', 40)->nullable();
            $table->string('public_email')->nullable();
            $table->string('website')->nullable();
            $table->text('description')->nullable();
            $table->string('photo_path')->nullable();
            $table->string('cover_path')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('accent_color', 7)->default('#000000');
            $table->string('status')->default('draft')->index();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'published_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('v_cards');
    }
};
