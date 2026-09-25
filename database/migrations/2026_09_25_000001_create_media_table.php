<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('type', 16)->index();          // image | video | embed
            $table->string('disk', 32)->default('uploads');
            $table->string('path')->nullable();           // original file, relative to disk
            $table->string('name')->nullable();           // original client file name
            $table->string('mime', 100)->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->json('variants')->nullable();          // {"480": "path.webp", ..., "full": "path.webp"}
            $table->foreignId('poster_id')->nullable()->constrained('media')->nullOnDelete();
            $table->string('embed_provider', 32)->nullable();
            $table->string('embed_url')->nullable();
            $table->string('embed_id')->nullable();
            $table->json('alt')->nullable();
            $table->json('caption')->nullable();
            $table->string('folder', 64)->nullable()->index();
            $table->string('color', 9)->nullable();        // dominant colour for placeholders
            $table->json('meta')->nullable();
            $table->string('legacy_path')->nullable()->index(); // original path from the old tables
            $table->timestamps();
        });

        // Which model uses which media (kept in sync from block JSON on save).
        Schema::create('mediables', function (Blueprint $table) {
            $table->foreignId('media_id')->constrained('media')->cascadeOnDelete();
            $table->morphs('mediable');
            $table->primary(['media_id', 'mediable_type', 'mediable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mediables');
        Schema::dropIfExists('media');
    }
};
