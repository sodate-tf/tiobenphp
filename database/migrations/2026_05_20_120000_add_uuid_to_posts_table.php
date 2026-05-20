<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('posts', 'uuid')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->uuid('uuid')->nullable()->after('lang');
            });
        }

        $posts = DB::table('posts')
            ->select(['id', 'uuid'])
            ->orderBy('id')
            ->get();

        foreach ($posts as $post) {
            if (!empty($post->uuid)) {
                continue;
            }

            DB::table('posts')
                ->where('id', $post->id)
                ->update(['uuid' => (string) Str::uuid()]);
        }

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE posts MODIFY uuid CHAR(36) NOT NULL');
        }

        try {
            Schema::table('posts', function (Blueprint $table) {
                $table->unique('uuid');
            });
        } catch (\Throwable $e) {
            // índice pode já existir em ambientes onde a coluna foi criada manualmente
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('posts', 'uuid')) {
            return;
        }

        try {
            Schema::table('posts', function (Blueprint $table) {
                $table->dropUnique('posts_uuid_unique');
            });
        } catch (\Throwable $e) {
            // índice pode não existir
        }

        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};
