<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('file_receives', function (Blueprint $table) {
            // We use dateTime to include the specific hour/minute for the 24hr window
            $table->dateTime('deadline_at')->nullable()->after('date_received');
        });
    }

    public function down(): void
    {
        Schema::table('file_receives', function (Blueprint $table) {
            $table->dropColumn('deadline_at');
        });
    }
};
