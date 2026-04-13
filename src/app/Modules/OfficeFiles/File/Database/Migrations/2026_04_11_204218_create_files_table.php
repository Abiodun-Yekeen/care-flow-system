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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->string('official_file_number')->nullable()->unique();
            $table->string('temp_file_number')->nullable()->unique();
            $table->boolean('is_temporary')->default(true);

            $table->string('subject');
            $table->text('description')->nullable();

            $table->string('source_name')->nullable();
            $table->string('source_reference_no')->nullable();
            $table->text('remark')->nullable();

            $table->string('status')->default('draft');
            $table->string('priority')->default('routine');

            $table->foreignId('current_department_id')->nullable();
            $table->foreignId('current_holder_user_id')->nullable();
            $table->foreignId('created_by')->nullable();
            $table->date('date_received')->nullable();
            $table->timestamp('last_movement_at')->nullable();
            $table->timestamp('closed_at')->nullable();

            $table->timestamps();

            $table->index('subject');
            $table->index('status');
            $table->index('date_received');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
