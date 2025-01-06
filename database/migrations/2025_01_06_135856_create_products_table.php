<?php

use App\Enums\ActiveStatus;
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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_catalog_id');
            $table->string('name');
            $table->foreignId('brand_id')->constrained()->onDelete('cascade');
            $table->enum('status', ActiveStatus::getValues())->default(ActiveStatus::Active->value);
            $table->timestamps();

            $table->foreign('product_catalog_id')->references('id')->on('product_catalog')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
