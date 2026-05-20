<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('pipeline_articles', function (Blueprint $table) {
      $table->uuid('id')->primary();

      $table->string('topic', 255);
      $table->string('agent', 50)->default('theme'); // saint | theme
      $table->string('language', 10)->default('pt-BR');
      $table->text('focus_keywords')->nullable();

      $table->date('date')->nullable(); // para saint
      $table->longText('source_text')->nullable();
      $table->longText('liturgy_source')->nullable();

      // Conteúdo gerado e formatado
      $table->longText('content_raw')->nullable();
      $table->longText('content_html')->nullable();

      // SEO final
      $table->string('title', 180)->nullable();
      $table->string('slug', 220)->nullable()->index();
      $table->text('meta_description')->nullable();
      $table->string('keywords', 500)->nullable();
      $table->string('cover_image_url', 2048)->nullable();

      $table->timestamp('published_at')->nullable();

      $table->timestamps();
    });
  }

  public function down(): void {
    Schema::dropIfExists('pipeline_articles');
  }
};
