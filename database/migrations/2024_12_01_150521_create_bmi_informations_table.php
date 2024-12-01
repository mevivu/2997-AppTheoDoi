<?php

use App\Enums\ActiveStatus;
use App\Enums\User\Gender;
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
        Schema::create('bmi_informations', function (Blueprint $table) {
            $table->id();
            $table->integer('age');
            $table->decimal('bmi', 10, 2);
            $table->enum('gender', Gender::getValues())->default(Gender::Other->value);
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
        Schema::dropIfExists('bmi_informations');
    }
};