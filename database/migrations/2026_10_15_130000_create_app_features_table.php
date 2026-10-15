<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration {
    private const MODULE_NAME = 'Quản lý chức năng App';
    private const PERMISSION = 'manageAppFeature';

    /**
     * Danh mục chức năng mặc định (tất cả BẬT để không ảnh hưởng app đang chạy).
     * code trùng với FeatureKeys (Flutter) và FeatureStatisticsService::getFeatureCatalog (BE).
     */
    private function features(): array
    {
        return [
            // [code, name, group, disabled_mode]
            ['memo_game', 'Luyện trí nhớ (Trang chủ)', 'home', 'hide'],
            ['referral', 'Kiếm tiền cùng Chăm con 360', 'account', 'hide'],
            ['video_education', 'Video giáo dục', 'education', 'maintenance'],
            ['pq', 'Chỉ số thể chất (PQ)', 'evaluation', 'maintenance'],
            ['iq', 'Chỉ số thông minh (IQ)', 'evaluation', 'maintenance'],
            ['gpa', 'Học bạ điện tử (GPA)', 'evaluation', 'maintenance'],
            ['eq', 'Chỉ số cảm xúc (EQ)', 'evaluation', 'maintenance'],
            ['aq', 'Chỉ số vượt khó (AQ)', 'evaluation', 'maintenance'],
            ['predict_height', 'Dự đoán và Phác đồ chiều cao', 'utility', 'maintenance'],
            ['care_knowledge', 'Kiến thức chăm con', 'utility', 'maintenance'],
            ['vaccine', 'Theo dõi lịch tiêm chủng', 'utility', 'maintenance'],
            ['pregnancy', 'Theo dõi thai kỳ', 'utility', 'maintenance'],
            ['clinic', 'Tìm phòng khám', 'utility', 'maintenance'],
            ['diary_prescription', 'Hồ sơ y tế', 'utility', 'maintenance'],
            ['diary_moment', 'Nhật ký khoảnh khắc', 'utility', 'maintenance'],
            ['develop', 'Quá trình phát triển', 'utility', 'maintenance'],
            ['store', 'Cửa hàng', 'utility', 'maintenance'],
        ];
    }

    public function up(): void
    {
        Schema::create('app_features', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique()->comment('Mã chức năng, trùng FeatureKeys');
            $table->string('name');
            $table->string('group', 30)->index()->comment('home|account|education|evaluation|utility');
            $table->boolean('enabled_android')->default(true);
            $table->boolean('enabled_ios')->default(true);
            $table->string('disabled_mode', 20)->default('hide')->comment('hide: ẩn hẳn | maintenance: hiện và báo bảo trì');
            $table->string('disabled_message')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->unsignedBigInteger('updated_by')->nullable()->comment('admin_id sửa gần nhất');
            $table->timestamps();
        });

        $now = now();
        $rows = [];
        foreach ($this->features() as $i => [$code, $name, $group, $mode]) {
            $rows[] = [
                'code' => $code,
                'name' => $name,
                'group' => $group,
                'enabled_android' => true,
                'enabled_ios' => true,
                'disabled_mode' => $mode,
                'sort_order' => $i + 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('app_features')->insert($rows);

        // Quyền quản trị + gán cho Super Admin
        DB::table('modules')->insertOrIgnore([
            'name' => self::MODULE_NAME,
            'description' => 'Bật/tắt các chức năng hiển thị trong app theo từng nền tảng',
            'status' => 2,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $modId = DB::table('modules')->where('name', self::MODULE_NAME)->value('id');

        DB::table('permissions')->insertOrIgnore([
            'title' => 'Quản lý chức năng App',
            'name' => self::PERMISSION,
            'type' => 'admin',
            'guard_name' => 'admin',
            'module_id' => $modId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $superAdminRole = DB::table('roles')->where('name', 'superAdmin')->first();
        $perm = DB::table('permissions')->where('name', self::PERMISSION)->first();
        if ($superAdminRole && $perm) {
            DB::table('role_has_permissions')->insertOrIgnore([
                'permission_id' => $perm->id,
                'role_id' => $superAdminRole->id,
            ]);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        Schema::dropIfExists('app_features');

        $permIds = DB::table('permissions')->where('name', self::PERMISSION)->pluck('id');
        DB::table('role_has_permissions')->whereIn('permission_id', $permIds)->delete();
        DB::table('permissions')->where('name', self::PERMISSION)->delete();
        DB::table('modules')->where('name', self::MODULE_NAME)->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
