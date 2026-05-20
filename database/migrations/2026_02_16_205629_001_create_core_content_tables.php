<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ai_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('agent_name')->default('Agente Tio Ben');
            $table->text('ai_model')->default('gemini-1.5-flash');
            $table->text('calendar_id')->nullable();
            $table->text('focus_keywords')->nullable();
            $table->text('remote_post_url')->nullable();
            $table->text('remote_post_api_key')->nullable();
            $table->text('json_format_template')->nullable();
            $table->text('writer_instructions')->nullable();
            $table->text('formatter_instructions')->nullable();
            $table->text('seo_instructions')->nullable();
            $table->json('writer_files')->nullable();
            $table->json('formatter_files')->nullable();
            $table->json('seo_files')->nullable();
            $table->timestamps(3);
        });

        Schema::create('articles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->dateTimeTz('generation_date', 3);
            $table->text('title');
            $table->longText('raw_content');
            $table->longText('formatted_content');
            $table->boolean('published')->default(false);
            // no PG era TEXT[]; aqui vira JSON (array)
            $table->json('keywords')->nullable();
            $table->text('meta_description')->nullable();

            $table->index('generation_date');
            $table->index('published');
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 255)->unique();
            $table->timestamps(3);
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title', 255);
            $table->string('slug', 255)->unique();
            $table->text('keywords')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('cover_image_url')->nullable();
            $table->longText('content');

            $table->uuid('category_id')->nullable();
            $table->boolean('is_active')->default(true);

            $table->dateTimeTz('publish_date', 3);
            $table->dateTimeTz('expiry_date', 3)->nullable();

            $table->timestamps(3);

            $table->index('category_id');
            $table->index('publish_date');

            $table->foreign('category_id')
                ->references('id')->on('categories')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('articles');
        Schema::dropIfExists('ai_settings');
    }
};
