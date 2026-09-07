<?php

use App\Enums\Package\PackageType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        if (!Schema::hasColumn('packages', 'max_devices')) {
            Schema::table('packages', function (Blueprint $table) {
                $table->unsignedInteger('max_devices')->default(1)->after('days')->comment('Số thiết bị đăng nhập tối đa cho phép');
            });
        }

        // Cập nhật mặc định: Gói 1 năm / VIP 1 năm (type = 12) tối đa 5 thiết bị
        DB::table('packages')
            ->where('type', PackageType::OneYear->value)
            ->update(['max_devices' => 5]);

        // Các gói 2 năm (type = 24) hoặc trọn đời nếu có cũng cho 5 thiết bị
        DB::table('packages')
            ->where('type', PackageType::TwoYear->value)
            ->update(['max_devices' => 5]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn('max_devices');
        });
    }
};
