<?php

use App\Enums\ApprovalStatus;
use App\Enums\Notification\MessageType;
use App\Enums\Notification\NotificationStatus;
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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->string('title');
            $table->text('message');
            $table->text('payment_confirmation_image')->nullable();
            $table->tinyInteger('status')->default(NotificationStatus::NOT_READ->value);
            $table->timestamp('read_at')->nullable();
            $table->unsignedBigInteger('user_id_attribute')->nullable();
            $table->enum('type', MessageType::getValues())->default(MessageType::UNCLASSIFIED->value);
            $table->enum('approval_status', ApprovalStatus::getValues())->default(ApprovalStatus::PENDING->value);
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');
            $table->foreignId('package_id')->nullable()->constrained('packages')->onDelete('set null');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
