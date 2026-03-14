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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title', 500);
            $table->text('description')->nullable();
            $table->longText('content')->nullable();
            $table->string('image_url', 500)->nullable();
            $table->string('author')->nullable();
            $table->timestamp('published_at')->index();

            $table->string('url', 500)->unique();
            $table->string('category')->index();
            $table->string('source')->index();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
