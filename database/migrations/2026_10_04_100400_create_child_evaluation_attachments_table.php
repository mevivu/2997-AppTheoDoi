<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('child_evaluation_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_evaluation_id')->constrained('child_evaluations')->cascadeOnDelete();
            $table->string('disk', 20)->default('local');
            $table->string('file_path');
            $table->string('original_name')->nullable();
            $table->string('mime_type', 50);
            $table->unsignedInteger('size_bytes');
            $table->unsignedSmallInteger('width')->nullable();
            $table->unsignedSmallInteger('height')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['child_evaluation_id', 'sort_order'], 'cea_eval_id_sort_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('child_evaluation_attachments');
    }
};
