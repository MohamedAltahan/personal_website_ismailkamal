<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Additive upgrade of the legacy tables. Legacy columns (designs.name, designs.thumbnail,
 * the images/videos tables…) are kept until `portfolio:migrate-legacy` has been verified.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ---- Projects (legacy "designs") ----
        DB::table('designs')->where('sub_category_id', '')->update(['sub_category_id' => null]);
        DB::table('designs')->where('category_id', '')->update(['category_id' => null]);

        Schema::table('designs', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable()->change();
            $table->unsignedBigInteger('sub_category_id')->nullable()->change();
            $table->string('name')->nullable()->change();

            $table->string('slug')->nullable()->unique()->after('id');
            $table->json('title')->nullable()->after('slug');
            $table->json('excerpt')->nullable()->after('title');
            $table->json('blocks')->nullable();
            $table->foreignId('cover_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->foreignId('hover_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->string('client')->nullable();
            $table->string('year', 16)->nullable();
            $table->json('tags')->nullable();
            $table->boolean('is_featured')->default(false)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamp('published_at')->nullable();
            $table->json('seo')->nullable();
            $table->unsignedBigInteger('views')->default(0);
        });

        // ---- Categories: names become {ar, en} JSON (spatie/laravel-translatable) ----
        foreach (['categories', 'sub_categories'] as $table) {
            DB::table($table)->orderBy('id')->each(function ($row) use ($table) {
                if (json_decode((string) $row->name) === null) {
                    DB::table($table)->where('id', $row->id)
                        ->update(['name' => json_encode(['ar' => $row->name, 'en' => $row->name], JSON_UNESCAPED_UNICODE)]);
                }
            });
        }

        Schema::table('categories', function (Blueprint $table) {
            $table->text('name')->change();
            $table->string('icon')->nullable()->change();
            $table->json('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
        });

        Schema::table('sub_categories', function (Blueprint $table) {
            $table->text('name')->change();
            $table->unsignedBigInteger('category_id')->change();
            $table->unsignedInteger('sort_order')->default(0);
        });

        // ---- Pages built with blocks (home, about, custom) ----
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->json('title');
            $table->json('blocks')->nullable();
            $table->json('seo')->nullable();
            $table->boolean('is_system')->default(false);
            $table->boolean('in_menu')->default(false);
            $table->string('status', 16)->default('active');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::table('socials', function (Blueprint $table) {
            $table->string('icon')->nullable()->change();
            $table->unsignedInteger('sort_order')->default(0);
        });

        Schema::table('email_inboxes', function (Blueprint $table) {
            $table->text('phone')->nullable()->change();
            $table->string('subject')->nullable();
            $table->string('locale', 5)->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamp('read_at')->nullable();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('locale', 5)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn('locale'));
        Schema::table('email_inboxes', fn (Blueprint $table) => $table->dropColumn(['subject', 'locale', 'ip', 'read_at']));
        Schema::table('socials', fn (Blueprint $table) => $table->dropColumn('sort_order'));
        Schema::dropIfExists('pages');
        Schema::table('sub_categories', fn (Blueprint $table) => $table->dropColumn('sort_order'));
        Schema::table('categories', fn (Blueprint $table) => $table->dropColumn(['description', 'sort_order']));

        Schema::table('designs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('cover_media_id');
            $table->dropConstrainedForeignId('hover_media_id');
            $table->dropColumn(['slug', 'title', 'excerpt', 'blocks', 'client', 'year', 'tags', 'is_featured', 'sort_order', 'published_at', 'seo', 'views']);
        });
    }
};
