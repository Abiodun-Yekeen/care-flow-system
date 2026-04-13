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
        Schema::create('file_openings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('file_id');
            $table->foreignId('requested_by_user_id')->nullable();
            $table->foreignId('requested_from_department_id')->nullable();
            $table->foreignId('opening_by_user_id')->nullable();

            $table->string('official_file_number')->nullable();
            $table->string('status')->default('pending');

            $table->text('request_note')->nullable();
            $table->text('response_note')->nullable();

            $table->timestamp('processed_at')->nullable();
            $table->timestamp('returned_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_openings');
    }
};





