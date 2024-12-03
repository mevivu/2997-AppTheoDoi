<?php

use App\Enums\Package\PackageUserStatus;
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
        Schema::create('user_packages', function (Blueprint $table) {
            $table->id();
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->enum('status', PackageUserStatus::getValues())->default(PackageUserStatus::Active->value);

            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('package_id')->constrained('packages')->onDelete('cascade');
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
        Schema::dropIfExists('user_packages');
    }
};
