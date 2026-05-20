<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::table('posts', function (Blueprint $table) {
      if (!Schema::hasColumn('posts', 'lang')) {
        $table->string('lang', 5)->default('pt')->index(); // pt, en
      }
      // opcional (recomendado) se você quiser controlar "PT/EN ativo"
      if (!Schema::hasColumn('posts', 'is_active')) {
        $table->boolean('is_active')->default(true)->index();
      }
    });
  }

  public function down(): void {
    Schema::table('posts', function (Blueprint $table) {
      if (Schema::hasColumn('posts', 'lang')) $table->dropColumn('lang');
    });
  }
};
