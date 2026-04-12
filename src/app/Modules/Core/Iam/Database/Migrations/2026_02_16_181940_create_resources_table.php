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
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // e.g., 'patients', 'lab_results'
            $table->string('name');
            $table->string('module_key'); // Which module it belongs to
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('module_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};
