<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('fixit_errors', function (Blueprint $table) {
            $table->string('fingerprint')->nullable()->index();
            $table->timestamp('last_seen_at')->nullable();
            $table->integer('occurrences')->default(1);
        });
    }

    public function down(): void
    {
        Schema::table('fixit_errors', function (Blueprint $table) {
            $table->dropColumn(['fingerprint', 'last_seen_at', 'occurrences']);
        });
    }
};

