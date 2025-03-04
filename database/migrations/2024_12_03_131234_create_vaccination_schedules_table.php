<?php

use App\Enums\ActiveStatus;
use App\Enums\Permission\PermissionType;
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
            $table->unsignedBigInteger('child_id')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('status', ActiveStatus::getValues())->default(ActiveStatus::Active->value);
            $table->date('performed_on')->nullable();
            $table->text('image')->nullable();
            $table->enum('type', PermissionType::getValues())->default(PermissionType::USER->value);
            $table->enum('vaccination_status', VaccinationStatus::getValues())->default(VaccinationStatus::NotVaccinated->value);
            $table->foreign('vaccination_type_id')->references('id')->on('vaccination_types')->onDelete('set null');
            $table->timestamps();
            $table->foreign('child_id')->references('id')->on('children')->onDelete('set null');

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
