<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('audits', function (Blueprint $table) {
            $table->json('recipients')->nullable()->after('target_url');
            $table->boolean('email_sent')->default(false)->after('recipients');
        });

        Schema::table('vulnerabilities', function (Blueprint $table) {
            $table->enum('status', ['open', 'accepted', 'false_positive', 'fixed'])
                  ->default('open')->after('cwe_id');
            $table->text('notes')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('vulnerabilities', function (Blueprint $table) {
            $table->dropColumn(['status', 'notes']);
        });
        Schema::table('audits', function (Blueprint $table) {
            $table->dropColumn(['recipients', 'email_sent']);
        });
    }
};
