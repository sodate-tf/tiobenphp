<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mobile_beta_testers', function (Blueprint $table) {
            $table->id();
            $table->string('google_email', 255)->unique();
            $table->string('whatsapp', 30);
            $table->text('source_url')->nullable();
            $table->boolean('link_sent')->default(false);
            $table->timestamp('link_sent_at')->nullable();
            $table->timestamps();

            $table->index('link_sent');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mobile_beta_testers');
    }
};
