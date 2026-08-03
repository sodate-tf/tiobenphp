<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('admin_generation_runs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('topic', 255);
            $table->string('status', 30)->default('queued')->index();
            $table->string('stage', 50)->default('queued');
            $table->text('message')->nullable();
            $table->uuid('pipeline_article_id')->nullable()->index();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_generation_runs');
    }
};

