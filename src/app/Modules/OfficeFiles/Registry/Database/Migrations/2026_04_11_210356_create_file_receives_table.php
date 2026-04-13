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
        Schema::create('file_receives', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->foreignId('file_id')->nullable();
            $table->foreignId('receive_department_id')->nullable();
            $table->foreignId('created_by')->nullable();

            $table->string('received_from')->nullable();
            $table->text('remark')->nullable();
            $table->string('status')->default('draft');

            $table->date('date_received')->nullable();
            $table->timestamp('submitted_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_receives');
    }
};

