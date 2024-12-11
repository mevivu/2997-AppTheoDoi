<?php

use App\Enums\ActiveStatus;
use App\Enums\User\Gender;
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
        Schema::create('weight_height_who', function (Blueprint $table) {
            $table->id();
            $table->decimal('weight', 5, 2)->nullable();
            $table->decimal('height', 5, 2)->nullable();
            $table->integer('age')->nullable();
            $table->integer('month')->nullable();
            $table->enum('gender', Gender::getValues())->default(Gender::Male->value);
            $table->enum('status', ActiveStatus::getValues())->default(ActiveStatus::Active->value);

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
        Schema::dropIfExists('weight_height_who');
    }
};
