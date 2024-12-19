<?php

use App\Enums\ActiveStatus;
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
        Schema::create('pregnancies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('child_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->tinyInteger('week')->nullable();
            $table->float('weight')->nullable();
            $table->integer('length')->nullable();
            $table->integer('head_circumference')->nullable();
            $table->text('image')->nullable();
            $table->enum('status', ActiveStatus::getValues())->default(ActiveStatus::Active->value);

            $table->timestamps();

            $table->foreign('child_id')->references('id')->on('children')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pregnancies');
    }
};
