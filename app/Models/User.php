<?php

namespace App\Models;

use App\Admin\Support\Eloquent\Sluggable;
use App\Enums\Package\PackageStatus;
use App\Enums\Package\PackageType;
use App\Enums\User\AffiliateRank;
use App\Enums\User\Gender;
use App\Enums\User\KycStatus;
use App\Enums\User\UserServiceType;
use App\Enums\User\UserStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * Mô hình User (Người dùng / Phụ huynh)
 *
 * Quản lý tài khoản người dùng, phụ huynh, xác thực JWT & Sanctum, phân quyền vai trò Spatie,
 * quản lý gói dịch vụ (UserPackage), giới hạn thiết bị đăng nhập (UserDevice), hệ thống tiếp thị
 * liên kết (Affiliate), hồ sơ định danh KYC và bảo mật dữ liệu nhạy cảm (mã hóa AES).
 */
class User extends Authenticatable implements JWTSubject
{
    use HasRoles, Sluggable, HasApiTokens, HasFactory, Notifiable;

    /**
     * Trường nguồn dùng để tự động tạo slug thân thiện SEO
     *
     * @var string
     */
    protected $columnSlug = 'fullname';

    /**
     * Danh sách các trường cho phép gán dữ liệu hàng loạt (Mass Assignment)
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',                    // Tên tài khoản người dùng
        'code',                        // Mã định danh người dùng
        'affiliate_code',              // Mã chia sẻ tiếp thị liên kết (Affiliate Code)
        'referrer_id',                 // ID của người dùng giới thiệu (Người bảo trợ)
        'affiliate_rank',              // Cấp bậc đối tác affiliate (1: Bạc, 2: Vàng, 3: Bạch Kim, 4: Kim Cương)
        'affiliate_total_sales',       // Tổng doanh số giới thiệu tích lũy (VNĐ)
        'wallet_balance',              // Số dư ví hoa hồng / tiền thưởng Affiliate (VNĐ)
        'slug',                        // Đường dẫn tĩnh định danh
        'fullname',                    // Họ và tên người dùng / phụ huynh
        'password',                    // Mật khẩu tài khoản (đã băm Bcrypt)
        'email',                       // Địa chỉ email (mã hóa AES)
        'phone',                       // Số điện thoại (mã hóa AES)
        'birthday',                    // Ngày tháng năm sinh
        'gender',                      // Giới tính (Enum Gender)
        'active',                      // Trạng thái kích hoạt tài khoản
        'avatar',                      // Đường dẫn ảnh đại diện
        'status',                      // Trạng thái hoạt động tài khoản (Enum UserStatus)
        'bank_id',                     // ID ngân hàng thụ hưởng liên kết
        'bank_code',                   // Mã ngân hàng (VCB, MB, TCB...)
        'bank_name',                   // Tên ngân hàng
        'bank_account_number',         // Số tài khoản ngân hàng nhận hoa hồng
        'bank_account_name',           // Tên chủ tài khoản ngân hàng
        'service_type',                // Phương thức đăng ký tài khoản (Email, Google, Apple)
        'apple_id',                    // Định danh tài khoản Apple ID (Sign in with Apple)
        'device_token',                // Token thiết bị nhận thông báo đẩy FCM
        'email_verified_at',           // Thời điểm xác thực email thành công
        'address',                     // Địa chỉ cư trú chi tiết
        'lat',                         // Tọa độ vĩ độ địa lý
        'lng',                         // Tọa độ kinh độ địa lý
        'father_name',                 // Họ và tên bố (phục vụ dự đoán chiều cao con)
        'father_height',               // Chiều cao của bố (cm)
        'father_birthday',             // Ngày sinh của bố
        'mother_name',                 // Họ và tên mẹ (phục vụ dự đoán chiều cao con)
        'mother_height',               // Chiều cao của mẹ (cm)
        'mother_birthday',             // Ngày sinh của mẹ
        'id_card_front',               // Ảnh chụp CCCD / CMND mặt trước (KYC)
        'id_card_back',                // Ảnh chụp CCCD / CMND mặt sau (KYC)
        'tax_code',                    // Mã số thuế cá nhân (MST)
        'kyc_status',                  // Trạng thái xác minh định danh (Enum KycStatus)
        'kyc_submitted_at',            // Thời điểm gửi hồ sơ yêu cầu xác minh KYC
        'kyc_verified_at',             // Thời điểm Admin duyệt xác minh KYC thành công
        'kyc_rejected_at',             // Thời điểm Admin từ chối hồ sơ KYC
        'kyc_rejection_reason',        // Lý do Admin từ chối duyệt hồ sơ KYC
        'pending_referral_reward',     // Đánh dấu tài khoản mới chưa hoàn thiện hồ sơ con (chống gian lận hoa hồng)
        'affiliate_terms_accepted_at', // Thời điểm người dùng đồng ý Điều khoản & Điều kiện Affiliate
    ];

    /**
     * Các trường dữ liệu ẩn đi khi chuyển đổi sang mảng hoặc JSON (Serialization)
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'remember_token', // Token ghi nhớ đăng nhập
        'password',       // Mật khẩu người dùng
    ];

    /**
     * Ép kiểu dữ liệu tự động cho các thuộc tính Eloquent Model
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'gender' => Gender::class,
        'active' => 'boolean',
        'status' => UserStatus::class,
        'service_type' => UserServiceType::class,
        'affiliate_rank' => AffiliateRank::class,
        'affiliate_total_sales' => 'decimal:0',
        'wallet_balance' => 'decimal:0',
        'kyc_status' => KycStatus::class,
        'kyc_submitted_at' => 'datetime',
        'kyc_verified_at' => 'datetime',
        'kyc_rejected_at' => 'datetime',
        'pending_referral_reward' => 'boolean',
        'affiliate_terms_accepted_at' => 'datetime',
    ];

    /**
     * Liên kết: Danh sách các gói dịch vụ / gói cước người dùng đã đăng ký
     *
     * @return HasMany
     */
    public function userPackages(): HasMany
    {
        return $this->hasMany(UserPackage::class);
    }

    /**
     * Liên kết: Danh sách tất cả thiết bị đã từng đăng nhập của người dùng
     *
     * @return HasMany
     */
    public function devices(): HasMany
    {
        return $this->hasMany(UserDevice::class, 'user_id');
    }

    /**
     * Liên kết: Danh sách các thiết bị đang ở trạng thái hoạt động (được phép phiên đăng nhập)
     *
     * @return HasMany
     */
    public function activeDevices(): HasMany
    {
        return $this->hasMany(UserDevice::class, 'user_id')->where('is_active', true);
    }

    /**
     * Liên kết: Người dùng đã giới thiệu tài khoản này (Người bảo trợ / Referrer)
     *
     * @return BelongsTo
     */
    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    /**
     * Liên kết: Danh sách những người dùng do tài khoản này giới thiệu (Tuyến dưới)
     *
     * @return HasMany
     */
    public function referrals(): HasMany
    {
        return $this->hasMany(User::class, 'referrer_id');
    }

    /**
     * Liên kết: Lịch sử nhận hoa hồng, điểm thưởng affiliate của tài khoản
     *
     * @return HasMany
     */
    public function affiliateHistories(): HasMany
    {
        return $this->hasMany(AffiliateHistory::class, 'user_id');
    }

    /**
     * Lấy số lượng thiết bị tối đa được phép đăng nhập theo gói cước hiện tại của người dùng
     *
     * @return int
     */
    public function getMaxDevicesAllowed(): int
    {
        // 1. Kiểm tra các gói đang hoạt động còn hạn, lấy số thiết bị lớn nhất
        $activeUserPackages = $this->userPackages()
            ->where('status', \App\Enums\Package\PackageUserStatus::Active)
            ->where('end_date', '>=', now())
            ->with('package')
            ->get();

        if ($activeUserPackages->isNotEmpty()) {
            $maxAllowed = (int) $activeUserPackages->max(function ($up) {
                return (int) ($up->package->max_devices ?? 1);
            });
            if ($maxAllowed > 0) {
                return $maxAllowed;
            }
        }

        // 2. Nếu không có gói trả phí còn hạn, kiểm tra gói gần nhất
        $firstPackage = $this->userPackages()->latest()->first();
        if ($firstPackage && $firstPackage->package) {
            return (int) ($firstPackage->package->max_devices ?? 1);
        }

        // 3. Dự phòng: Lấy giới hạn từ Gói Miễn phí / Cơ bản mặc định
        $normalPackage = Package::getNormalPackage();
        if ($normalPackage) {
            return (int) ($normalPackage->max_devices ?? 1);
        }

        return 1;
    }

    /**
     * Liên kết: Danh sách hồ sơ các con của phụ huynh này
     *
     * @return HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(Child::class, 'user_id');
    }

    /**
     * Liên kết: Danh sách lịch sử giao dịch mua gói / nạp tiền của người dùng
     *
     * @return HasMany
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'user_id');
    }

    /**
     * Liên kết: Thông tin ngân hàng thụ hưởng liên kết của người dùng
     *
     * @return BelongsTo
     */
    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class, 'bank_id');
    }

    /**
     * Liên kết: Phân quyền vai trò người dùng (Spatie Roles)
     *
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'model_has_roles', 'model_id', 'role_id')
            ->withPivot('model_type')
            ->wherePivot('model_type', self::class);
    }

    /**
     * Kiểm tra người dùng có ít nhất một quyền (permission) trong danh sách chỉ định hay không
     *
     * @param array $permissionsArr
     * @return bool
     */
    public function checkPermissions($permissionsArr): bool
    {
        foreach ($permissionsArr as $permission) {
            if ($this->can($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Lấy định danh khóa chính đại diện cho người dùng khi tạo JWT Token
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Trả về mảng chứa các thông tin tùy biến (Custom Claims) bổ sung vào JWT Token
     *
     * @return array
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }

    /**
     * Khởi tạo các sự kiện vòng đời (Model Lifecycle Events) của mô hình User
     *
     * @return void
     */
    protected static function booted(): void
    {
        // Tự động sinh mã affiliate CC001, CC002... khi tạo người dùng mới
        static::creating(function ($user) {
            if (empty($user->affiliate_code)) {
                $user->affiliate_code = static::generateAffiliateCode();
            }
        });

        // Tự động kích hoạt gói dùng thử (Trial Package) khi tài khoản đăng ký mới
        static::created(function ($user) {
            $trialPackage = Package::getTrialPackage();
            if ($trialPackage) {
                $user->userPackages()->create([
                    'package_id' => $trialPackage->id,
                    'start_date' => now(),
                    'end_date' => now()->addDays($trialPackage->days),
                    'status' => PackageStatus::Active,
                    'current_type' => PackageType::Trial
                ]);
            }
        });
    }

    /**
     * Tự động sinh mã chia sẻ tiếp thị liên kết định dạng chuẩn CC001, CC002...
     *
     * @return string
     */
    public static function generateAffiliateCode(): string
    {
        $maxCode = static::where('affiliate_code', 'LIKE', 'CC%')
            ->orderByRaw('CAST(SUBSTRING(affiliate_code, 3) AS UNSIGNED) DESC')
            ->value('affiliate_code');

        $nextNumber = 1;
        if ($maxCode && preg_match('/^CC(\d+)$/', $maxCode, $matches)) {
            $nextNumber = (int) $matches[1] + 1;
        }

        $code = sprintf('CC%03d', $nextNumber);

        while (static::where('affiliate_code', $code)->exists()) {
            $nextNumber++;
            $code = sprintf('CC%03d', $nextNumber);
        }

        return $code;
    }

    /**
     * Lấy tên hiển thị cấp bậc đối tác tiếp thị liên kết (Bạc, Vàng, Bạch Kim, Kim Cương)
     *
     * @return string
     */
    public function getAffiliateRankName(): string
    {
        return $this->affiliate_rank?->name() ?? 'Bạc';
    }

    /**
     * Lấy class CSS huy hiệu (Badge) hiển thị cấp bậc affiliate trên giao diện Quản trị (Admin)
     *
     * @return string
     */
    public function getAffiliateRankBadge(): string
    {
        return $this->affiliate_rank?->badge() ?? 'bg-secondary-lt';
    }

    /**
     * Kiểm tra đối tác đã hoàn thành và được Admin phê duyệt xác minh KYC (CCCD + MST) thành công hay chưa
     *
     * @return bool
     */
    public function hasCompletedKyc(): bool
    {
        return $this->kyc_status === KycStatus::APPROVED || !empty($this->kyc_verified_at);
    }

    /**
     * Kiểm tra hồ sơ xác minh KYC có đang chờ Admin xét duyệt hay không
     *
     * @return bool
     */
    public function isKycPending(): bool
    {
        return $this->kyc_status === KycStatus::PENDING;
    }

    /**
     * Kiểm tra hồ sơ xác minh KYC có bị Admin từ chối hay không
     *
     * @return bool
     */
    public function isKycRejected(): bool
    {
        return $this->kyc_status === KycStatus::REJECTED;
    }

    /**
     * Lấy class CSS huy hiệu (Badge) thể hiện màu sắc của trạng thái xác minh KYC
     *
     * @return string
     */
    public function getKycStatusBadge(): string
    {
        return $this->kyc_status?->badge() ?? 'bg-secondary-lt text-secondary';
    }

    /**
     * Kiểm tra người dùng đã tạo ít nhất 1 hồ sơ con trong hệ thống hay chưa
     *
     * @return bool
     */
    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }

    /**
     * Kiểm tra người dùng đã xác nhận đồng ý Điều kiện & Điều khoản Affiliate hay chưa
     *
     * @return bool
     */
    public function hasAcceptedAffiliateTerms(): bool
    {
        return !empty($this->affiliate_terms_accepted_at);
    }

    /**
     * Thuộc tính ảo: Lấy số điện thoại đã được giải mã AES an toàn
     *
     * @return string|null
     */
    public function getDecryptedPhoneAttribute(): ?string
    {
        if (empty($this->phone)) {
            return null;
        }
        try {
            $decrypted = \App\AES\AESHelper::decrypt($this->phone);
            return ($decrypted !== false && !empty($decrypted)) ? $decrypted : $this->phone;
        } catch (\Throwable $e) {
            return $this->phone;
        }
    }

    /**
     * Thuộc tính ảo: Lấy địa chỉ email đã được giải mã AES an toàn
     *
     * @return string|null
     */
    public function getDecryptedEmailAttribute(): ?string
    {
        if (empty($this->email)) {
            return null;
        }
        try {
            $decrypted = \App\AES\AESHelper::decrypt($this->email);
            return ($decrypted !== false && !empty($decrypted)) ? $decrypted : $this->email;
        } catch (\Throwable $e) {
            return $this->email;
        }
    }
}
