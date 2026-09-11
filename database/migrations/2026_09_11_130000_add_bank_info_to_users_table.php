<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('bank_id')->nullable()->after('status')->comment('ID ngân hàng liên kết từ bảng banks');
            $table->string('bank_code', 50)->nullable()->after('bank_id')->comment('Mã ngân hàng (VCB, MB, TCB...)');
            $table->string('bank_name', 150)->nullable()->after('bank_code')->comment('Tên ngân hàng');
            // bank_account_number đã tồn tại trong bảng users
            $table->string('bank_account_name', 150)->nullable()->after('bank_account_number')->comment('Tên chủ tài khoản');

            $table->foreign('bank_id')->references('id')->on('banks')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['bank_id']);
            $table->dropColumn([
                'bank_id',
                'bank_code',
                'bank_name',
                'bank_account_name',
            ]);
        });
    }
};
