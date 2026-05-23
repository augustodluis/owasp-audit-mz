<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_id')->unique()->constrained()->cascadeOnDelete();
            $table->timestamp('generated_at')->nullable();
            $table->enum('format', ['html', 'pdf'])->default('html');
            $table->string('file_path', 512)->nullable();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['info', 'success', 'warning', 'critical'])->default('info');
            $table->text('message');
            $table->boolean('read_flag')->default(false);
            $table->timestamps();
            $table->index('user_id');
        });

        Schema::create('error_logs', function (Blueprint $table) {
            $table->id();
            $table->enum('level', ['error', 'warning', 'critical'])->default('error');
            $table->text('message');
            $table->text('stack_trace')->nullable();
            $table->json('context')->nullable();
            $table->string('file', 512)->nullable();
            $table->integer('line')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('created_at')->nullable();
            $table->index('level');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('error_logs');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('reports');
    }
};
