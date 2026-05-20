<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        /**
         * USERS
         * Laravel já cria a tabela users em:
         * 0001_01_01_000000_create_users_table
         * Aqui só adicionamos campos que você quer para futuro (mobile/admin).
         */
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role', 30)->default('user')->after('password');
            }
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('role');
            }
        });

        /**
         * PUSH
         */
        Schema::create('push_notifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title', 255)->nullable();
            $table->text('body')->nullable();
            $table->string('type', 50)->nullable();
            $table->json('payload')->nullable();

            $table->dateTime('scheduled_at', 3)->nullable();
            $table->dateTime('sent_at', 3)->nullable();

            $table->dateTime('created_at', 3)->useCurrent();
            $table->unsignedInteger('delivered_count')->default(0);
            $table->unsignedInteger('opened_count')->default(0);

            $table->index('scheduled_at');
            $table->index('sent_at');
        });

        Schema::create('push_events', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // “certo” para MySQL: FK para push_notifications.id
            $table->unsignedBigInteger('notification_id')->nullable();

            // compatibilidade com seu PG atual (notification_id UUID)
            $table->uuid('notification_uuid')->nullable();

            $table->string('event_type', 30)->nullable();
            $table->dateTime('created_at', 3)->useCurrent();

            $table->index('notification_id');
            $table->index('notification_uuid');

            $table->foreign('notification_id')
                ->references('id')->on('push_notifications')
                ->nullOnDelete();
        });

        Schema::create('push_tokens', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('expo_token', 255)->unique();

            // futuro: associar token a user
            $table->unsignedBigInteger('user_id')->nullable();

            $table->string('platform', 30)->nullable(); // ios|android|web
            $table->dateTime('created_at', 3)->useCurrent();
            $table->dateTime('last_seen_at', 3)->nullable();

            $table->index('user_id');

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('push_tokens');
        Schema::dropIfExists('push_events');
        Schema::dropIfExists('push_notifications');

        // Remover colunas adicionadas em users (opcional, mas correto)
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'is_active')) {
                $table->dropColumn('is_active');
            }
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
        });
    }
};
