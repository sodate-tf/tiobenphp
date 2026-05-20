<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->boolean('is_featured')
                ->default(false)
                ->after('is_active');

            $table->index(['lang', 'is_featured']);
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex(['lang', 'is_featured']);
            $table->dropColumn('is_featured');
        });
    }
};
