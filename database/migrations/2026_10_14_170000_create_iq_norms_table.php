<?php

use App\Enums\ActiveStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('iq_norms')) {
            Schema::create('iq_norms', function (Blueprint $table) {
                $table->id();
                $table->unsignedTinyInteger('age')->unique()->comment('Độ tuổi của trẻ (1 - 16 tuổi)');
                $table->decimal('mean_score', 6, 2)->comment('Điểm thô trung bình nhóm tuổi (Mean μ)');
                $table->decimal('sd_score', 6, 2)->comment('Độ lệch chuẩn nhóm tuổi (StdDev σ)');
                $table->unsignedInteger('sample_size')->default(0)->comment('Số lượng trẻ trong mẫu khảo sát (N)');
                $table->decimal('min_raw_score', 6, 2)->nullable()->comment('Điểm thô thấp nhất ghi nhận');
                $table->decimal('max_raw_score', 6, 2)->nullable()->comment('Điểm thô cao nhất ghi nhận');
                $table->string('version', 30)->default('baseline_v1')->comment('Phiên bản bảng chuẩn');
                $table->string('status')->default(ActiveStatus::Active->value)->comment('Trạng thái kích hoạt');
                $table->text('note')->nullable()->comment('Ghi chú của chuyên gia / thông tin mẫu');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('iq_norms');
    }
};
