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
        Schema::create('file_transfers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('file_id');
            $table->foreignId('requesting_department_id');
            $table->foreignId('requesting_user_id')->nullable();

            $table->foreignId('holding_department_id');
            $table->foreignId('holding_user_id')->nullable();

            $table->string('status')->default('pending');
            $table->text('request_note')->nullable();
            $table->text('response_note')->nullable();

            $table->timestamp('requested_at')->nullable();
            $table->timestamp('transferred_at')->nullable();
            $table->timestamp('responded_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_transfers');
    }
};







