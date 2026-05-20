<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'slug')) {
                $table->string('slug', 255)->nullable()->after('name');
                $table->unique('slug');
            }

            if (!Schema::hasColumn('categories', 'description')) {
                $table->text('description')->nullable()->after('slug');
            }

            if (!Schema::hasColumn('categories', 'meta_title')) {
                $table->string('meta_title', 255)->nullable()->after('description');
            }

            if (!Schema::hasColumn('categories', 'meta_description')) {
                $table->text('meta_description')->nullable()->after('meta_title');
            }
        });

        if (Schema::hasColumn('categories', 'slug')) {
            $used = [];

            $categories = DB::table('categories')
                ->select(['id', 'name', 'slug'])
                ->orderBy('name')
                ->get();

            foreach ($categories as $category) {
                if (!empty($category->slug)) {
                    $used[$category->slug] = true;
                    continue;
                }

                $base = Str::slug((string) $category->name) ?: (string) $category->id;
                $slug = $base;
                $i = 2;

                while (isset($used[$slug]) || DB::table('categories')->where('slug', $slug)->exists()) {
                    $slug = $base . '-' . $i;
                    $i++;
                }

                $used[$slug] = true;

                DB::table('categories')
                    ->where('id', $category->id)
                    ->update(['slug' => $slug, 'updated_at' => now()]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'slug')) {
                try {
                    $table->dropUnique('categories_slug_unique');
                } catch (Throwable $e) {
                    // índice pode já ter sido removido manualmente
                }
                $table->dropColumn('slug');
            }

            foreach (['description', 'meta_title', 'meta_description'] as $column) {
                if (Schema::hasColumn('categories', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
