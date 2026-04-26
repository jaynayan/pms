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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('creator_id')->constrained('users');
            $table->foreignId('assigned_to')->nullable()->constrained('users');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status')->default('To Do');
            $table->string('priority')->default('Medium');
            $table->date('due_date')->nullable();
            $table->boolean('is_backlog')->default(true);
            $table->integer('position')->default(0);
            $table->decimal('original_estimate', 8, 2)->nullable();
            $table->decimal('total_spent', 8, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
