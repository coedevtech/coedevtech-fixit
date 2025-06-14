<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fixit_errors', function (Blueprint $table) {
            $table->id();
            $table->string('url')->index();
            $table->json('request')->nullable();
            $table->json('response')->nullable();
            $table->string('ip')->nullable()->index();
            $table->string('exception')->nullable();
            $table->text('file')->nullable();
            $table->integer('line')->nullable();
            $table->longText('trace')->nullable();
            $table->enum('status', ['not_fixed', 'fixed'])->default('not_fixed')->index();
            $table->timestamps();

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fixit_errors');
    }
};

