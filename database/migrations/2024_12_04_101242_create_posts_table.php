<?php

use App\Enums\FeaturedStatus;
use App\Enums\Post\PostStatus;
use App\Enums\PriorityStatus;
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
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('image');
            $table->tinyInteger('is_featured')->default(FeaturedStatus::Featureless->value);
            $table->tinyInteger('status')->default(PostStatus::Draft->value);
            $table->text('excerpt')->nullable();
            $table->longText('content')->nullable();
            $table->tinyInteger('priority')->default(PriorityStatus::NotPriority->value);
            $table->tinyInteger('post_type');
            $table->dateTime('posted_at');
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
        Schema::dropIfExists('posts');
    }
};
