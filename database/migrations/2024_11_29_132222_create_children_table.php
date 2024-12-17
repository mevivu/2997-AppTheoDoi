<?php

use App\Enums\Child\BornStatus;
use App\Enums\Child\ChildStatus;
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
    public function up(): void
    {
        Schema::create('children', function (Blueprint $table) {
            $table->id();
            $table->string('fullname');
            $table->date('birthday')->nullable();
            $table->enum('gender', Gender::getValues())->default(Gender::Other->value);
            $table->enum('is_born', BornStatus::getValues())->default(BornStatus::Unborn->value);
            $table->text('avatar')->nullable();
            $table->enum('status', ChildStatus::getValues())->default(ChildStatus::Active->value);

            $table->timestamps();

            $table->foreignId('user_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('children');
    }
};
