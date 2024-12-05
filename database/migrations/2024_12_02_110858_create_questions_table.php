<?php

use App\Enums\ActiveStatus;
use App\Enums\Question\QuestionType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('question_group_id')->nullable();
            $table->integer('age')->nullable();
            $table->string('question');
            $table->enum('question_type', QuestionType::getValues())->default(QuestionType::IQ->value);
            $table->enum('status', ActiveStatus::getValues())->default(ActiveStatus::Active->value);
            $table->foreign('question_group_id')->references('id')->on('question_groups')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('questions');
    }
};
