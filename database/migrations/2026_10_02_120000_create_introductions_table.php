<?php

use App\Enums\DefaultStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('introductions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            /** 1: Giới thiệu chung/Về chúng tôi, 2: Tầm nhìn, 3: Sứ mệnh, 4: Giá trị cốt lõi, 5: Thương hiệu & Pháp lý */
            $table->tinyInteger('section_type')->default(1);
            $table->text('image')->nullable();
            $table->string('icon')->nullable();
            $table->text('excerpt')->nullable();
            $table->longText('content')->nullable();
            $table->integer('sort_order')->default(0);
            $table->tinyInteger('status')->default(DefaultStatus::Published->value);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('introductions');
    }
};
