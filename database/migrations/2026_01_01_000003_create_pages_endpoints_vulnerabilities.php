<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_id')->constrained()->cascadeOnDelete();
            $table->string('url', 2048);
            $table->smallInteger('http_status')->nullable();
            $table->timestamp('discovered_at')->nullable();
            $table->index('audit_id');
        });

        Schema::create('endpoints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained()->cascadeOnDelete();
            $table->enum('method', ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS']);
            $table->json('parameters')->nullable();
            $table->index('page_id');
        });

        Schema::create('vulnerabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('endpoint_id')->constrained()->cascadeOnDelete();
            $table->string('check_code', 60);
            $table->string('name');
            $table->enum('risk', ['High', 'Medium', 'Low', 'Informational']);
            $table->enum('confidence', ['High', 'Medium', 'Low'])->nullable();
            $table->text('description')->nullable();
            $table->text('solution')->nullable();
            $table->string('reference', 2048)->nullable();
            $table->integer('cwe_id')->nullable();
            $table->index(['endpoint_id', 'risk']);
            $table->index('check_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vulnerabilities');
        Schema::dropIfExists('endpoints');
        Schema::dropIfExists('pages');
    }
};
