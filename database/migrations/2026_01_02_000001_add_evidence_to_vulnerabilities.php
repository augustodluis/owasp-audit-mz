<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('vulnerabilities', function (Blueprint $table) {
            $table->string('owasp_category', 80)->nullable()->after('check_code');
            $table->text('evidence')->nullable()->after('description');
            $table->text('bad_example')->nullable()->after('evidence');
            $table->text('good_example')->nullable()->after('bad_example');
        });
    }

    public function down(): void
    {
        Schema::table('vulnerabilities', function (Blueprint $table) {
            $table->dropColumn(['owasp_category', 'evidence', 'bad_example', 'good_example']);
        });
    }
};
