<?php

use App\Enums\Setting\SettingGroup;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('settings')
            ->whereIn('setting_key', ['evaluation_guide_video', 'evaluation_guide_note'])
            ->update(['group' => SettingGroup::Evaluation]);
    }

    public function down(): void
    {
        DB::table('settings')
            ->whereIn('setting_key', ['evaluation_guide_video', 'evaluation_guide_note'])
            ->update(['group' => SettingGroup::General]);
    }
};
