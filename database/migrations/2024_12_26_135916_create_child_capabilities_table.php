<?php

use App\Enums\ChildEvaluation\EvaluationStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('child_capabilities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('child_evaluation_id');
            $table->unsignedBigInteger('capability_id');
            $table->enum('capability_status', EvaluationStatus::getValues())->default(EvaluationStatus::NotAchieved->value);

            $table->foreign('child_evaluation_id')->references('id')->on('child_evaluations')->onDelete('cascade');
            $table->foreign('capability_id')->references('id')->on('capabilities')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('child_capabilities');
    }
};
