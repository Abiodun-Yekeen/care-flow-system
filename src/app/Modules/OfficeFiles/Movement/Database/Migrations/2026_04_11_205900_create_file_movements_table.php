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
        Schema::create('file_movements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('file_id');
            $table->foreignId('from_department_id')->nullable();
            $table->foreignId('from_user_id')->nullable();
            $table->foreignId('to_department_id')->nullable();
            $table->foreignId('to_user_id')->nullable();

            $table->foreignId('acted_by_user_id')->nullable();
            $table->foreignId('received_by_user_id')->nullable();

            $table->string('movement_type')->nullable();
            $table->string('movement_status')->default('completed');

            $table->text('remarks')->nullable();
            $table->text('minute')->nullable();

            $table->timestamp('acted_at')->nullable();
            $table->timestamp('received_at')->nullable();

            $table->timestamps();

            $table->index(['file_id', 'movement_type']);
            $table->index('movement_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_movements');
    }
};




