<?php

use App\Enums\ActiveStatus;
use App\Enums\Vaccination\VaccinationStatus;
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
        Schema::create('vaccination_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vaccination_type_id')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('status', ActiveStatus::getValues())->default(ActiveStatus::Active->value);
            $table->date('performed_on')->nullable();
            $table->text('image')->nullable();
            $table->enum('vaccination_status', VaccinationStatus::getValues())->default(VaccinationStatus::NotVaccinated->value);
            $table->foreignId('child_id')->constrained('children')->onDelete('cascade');
            $table->foreign('vaccination_type_id')->references('id')->on('vaccination_types')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('vaccination_schedules');
    }
};
