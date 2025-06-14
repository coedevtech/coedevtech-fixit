<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('fixit_errors', function (Blueprint $table) {
            if (!Schema::hasColumn('fixit_errors', 'fingerprint')) {
                $table->string('fingerprint')->nullable()->index()->after('trace');
            }

            if (!Schema::hasColumn('fixit_errors', 'last_seen_at')) {
                $table->timestamp('last_seen_at')->nullable()->after('fingerprint');
            }
            
            if (!Schema::hasColumn('fixit_errors', 'occurrences')) {
                $table->integer('occurrences')->default(1)->after('last_seen_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('fixit_errors', function (Blueprint $table) {
            $table->dropColumn(['fingerprint', 'last_seen_at', 'occurrences']);
        });
    }
};

