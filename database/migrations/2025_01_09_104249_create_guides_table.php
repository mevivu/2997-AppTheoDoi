<?php

use App\Enums\ActiveStatus;
use App\Enums\Guide\GuideType;
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
        Schema::create('guides', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('note')->nullable();
            $table->enum('type', GuideType::getValues())->default(GuideType::Strength->value);
            $table->enum('status', ActiveStatus::getValues())->default(ActiveStatus::Draft->value);
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
        Schema::dropIfExists('guides');
    }
};
