<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pipeline_articles', function (Blueprint $table) {
            $table->string('moderation_status', 30)
                ->default('pending')
                ->after('published_at')
                ->index();

            $table->json('quality_report')->nullable()->after('moderation_status');
            $table->timestamp('quality_checked_at')->nullable()->after('quality_report');

            $table->boolean('auto_published')->default(false)->after('quality_checked_at');

            $table->text('review_notes')->nullable()->after('auto_published');
            $table->text('rejection_reason')->nullable()->after('review_notes');
            $table->timestamp('reviewed_at')->nullable()->after('rejection_reason');
            $table->unsignedBigInteger('reviewed_by')->nullable()->after('reviewed_at');
        });
    }

    public function down(): void
    {
        Schema::table('pipeline_articles', function (Blueprint $table) {
            $table->dropColumn([
                'moderation_status',
                'quality_report',
                'quality_checked_at',
                'auto_published',
                'review_notes',
                'rejection_reason',
                'reviewed_at',
                'reviewed_by',
            ]);
        });
    }
};

