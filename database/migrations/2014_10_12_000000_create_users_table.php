<?php

use App\Enums\User\UserStatus;
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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->char('code', 50);
            $table->char('username', 100)->unique()->nullable();
            $table->string('slug')->unique();
            $table->string('fullname');
            $table->char('email', 100)->unique()->nullable();
            $table->char('phone', 20)->unique()->nullable();
            $table->text('avatar')->nullable();
            $table->date('birthday')->nullable();
            $table->string('device_token')->nullable();
            $table->tinyInteger('gender');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('token_get_password')->nullable();
            $table->string('password');
            $table->boolean('active')->default(true);
            $table->tinyInteger('status')->default(UserStatus::Active->value);
            $table->string('bank_account_number', 50)->nullable();
            $table->string('address');
            $table->double('latitude', 15, 10)->nullable();
            $table->double('longitude', 15, 10)->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
