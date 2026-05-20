<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('liturgy_days', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('liturgy_date')->unique();

            // URL/slug real da sua página de liturgia (SEO)
            $table->string('page_url', 255)->unique();

            $table->string('title', 255)->nullable();
            $table->timestamps(3);
        });

        Schema::create('liturgy_post_links', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('liturgy_day_id');
            $table->uuid('post_id');

            // parágrafo único que aparece nos dois lados
            $table->text('paragraph');

            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);

            $table->timestamps(3);

            $table->unique(['liturgy_day_id', 'post_id']);
            $table->index('post_id');

            $table->foreign('liturgy_day_id')
                ->references('id')->on('liturgy_days')
                ->cascadeOnDelete();

            $table->foreign('post_id')
                ->references('id')->on('posts')
                ->cascadeOnDelete();
        });

        Schema::create('post_cross_links', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('post_id');
            $table->uuid('linked_post_id');

            // parágrafo exibido no post e no post linkado
            $table->text('paragraph');

            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);

            $table->timestamps(3);

            $table->unique(['post_id', 'linked_post_id']);
            $table->index('linked_post_id');

            $table->foreign('post_id')
                ->references('id')->on('posts')
                ->cascadeOnDelete();

            $table->foreign('linked_post_id')
                ->references('id')->on('posts')
                ->cascadeOnDelete();
        });

        Schema::create('post_related_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('post_id');
            $table->uuid('related_post_id');

            $table->integer('sort_order')->default(0);
            $table->dateTime('created_at', 3)->useCurrent();

            $table->unique(['post_id', 'related_post_id']);
            $table->index('related_post_id');

            $table->foreign('post_id')
                ->references('id')->on('posts')
                ->cascadeOnDelete();

            $table->foreign('related_post_id')
                ->references('id')->on('posts')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_related_items');
        Schema::dropIfExists('post_cross_links');
        Schema::dropIfExists('liturgy_post_links');
        Schema::dropIfExists('liturgy_days');
    }
};
