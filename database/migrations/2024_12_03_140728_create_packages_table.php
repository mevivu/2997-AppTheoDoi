<?php

use App\Enums\Package\PackageStatus;
use App\Enums\Package\PackageType;
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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 15, 0);
            $table->text('description')->nullable();
            $table->enum('status', PackageStatus::getValues())->default(PackageStatus::Active->value);
            $table->enum('type', PackageType::getValues())->default(PackageType::ThreeMonths->value);
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
        Schema::dropIfExists('packages');
    }
};
