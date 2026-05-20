<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('post_cross_links', function (Blueprint $table) {
            // data do vínculo (liturgia do dia)
            $table->date('link_date')->nullable()->index()->after('linked_post_id');
        });
    }

    public function down(): void
    {
        Schema::table('post_cross_links', function (Blueprint $table) {
            $table->dropIndex(['link_date']);
            $table->dropColumn('link_date');
        });
    }
};
