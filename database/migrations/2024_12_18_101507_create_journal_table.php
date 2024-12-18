<?php

use App\Enums\Journal\JournalType;
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
    public function up(): void
    {
        Schema::create('journals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('child_id');
            $table->string('title');
            $table->text('content');
            $table->text('image');
            $table->enum('type', JournalType::getValues())->default(JournalType::Moment->value);

            $table->timestamps();
            $table->foreign('child_id')->references('id')->on('children')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('journals');
    }
};
