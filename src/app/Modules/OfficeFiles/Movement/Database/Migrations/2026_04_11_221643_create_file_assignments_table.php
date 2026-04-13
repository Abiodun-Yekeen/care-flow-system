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
        Schema::create('file_assignments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('file_id');
            $table->foreignId('assigned_by_user_id')->nullable();
            $table->foreignId('assigned_to_user_id')->nullable();
            $table->foreignId('assigned_to_department_id')->nullable();

            $table->string('assignment_type')->nullable();
            $table->string('status')->default('active');
            $table->text('remark')->nullable();

            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_assignments');
    }
};



