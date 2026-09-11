<?php

namespace App\Models;

use App\Admin\Support\Eloquent\Sluggable;
use App\Enums\Package\PackageStatus;
use App\Enums\Package\PackageType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Enums\User\{Gender, UserStatus, UserServiceType, AffiliateRank};

class User extends Authenticatable implements JWTSubject
{
    use HasRoles, Sluggable, HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $columnSlug = 'fullname';

    

    protected $fillable = [
        /** Tên người dùng */
        'username',
        /** Mã người dùng */
        'code',
        /** Mã chia sẻ affiliate */
        'affiliate_code',
        /** ID người giới thiệu */
        'referrer_id',
        /** Cấp bậc mẹ giới thiệu (1: Mẹ Đồng, 2: Mẹ Bạc, 3: Mẹ Vàng, 4: Mẹ Kim Cương) */
        'affiliate_rank',
        /** Tổng doanh số giới thiệu tích lũy (VNĐ) */
        'affiliate_total_sales',
        /** Số dư ví hoa hồng / thưởng Affiliate (VNĐ) */
        'wallet_balance',
        /** Đường dẫn tĩnh */
        'slug',
        /** Họ và tên */
        'fullname',
        /** Mật khẩu */
        'password',
        /** Email */
        'email',
        /** Số điện thoại */
        'phone',
        /** Ngày sinh */
        'birthday',
        /** Giới tính */
        'gender',
        /** Trạng thái hoạt động */
        'active',
        /** Ảnh đại diện */
        'avatar',
        /** Trạng thái */
        'status',
        /** ID ngân hàng liên kết */
        'bank_id',
        /** Mã ngân hàng (VCB, MB, TCB...) */
        'bank_code',
        /** Tên ngân hàng */
        'bank_name',
        /** Số tài khoản ngân hàng */
        'bank_account_number',
        /** Tên chủ tài khoản */
        'bank_account_name',
        /** Hình thức đăng ký (Email, Google, Apple) */
        'service_type',
        /** Apple ID */
        'apple_id',
        /** Token thiết bị */
        'device_token',
        /** Thời gian xác thực email */
        'email_verified_at',
        /** Tên địa chỉ chi tiết*/
        'address',
        /** Vĩ độ */
        'lat',
        /** Kinh độ */
        'lng',
        /** Tên của bố */
        'father_name',
        /** Chiều cao của bố (cm) */
        'father_height',
        /** Ngày sinh của bố */
        'father_birthday',
        /** Tên của mẹ */
        'mother_name',
        /** Chiều cao của mẹ */
        'mother_height',
        /** Ngày sinh của mẹ */
        'mother_birthday',

    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'remember_token',
        'password'
    ];

    /**
     * The attributes that should be cast.
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
    ];

    public function userPackages(): HasMany
    {
        return $this->hasMany(UserPackage::class);
    }

    public function devices(): HasMany
    {
        return $this->hasMany(UserDevice::class, 'user_id');
    }

    public function activeDevices(): HasMany
    {
        return $this->hasMany(UserDevice::class, 'user_id')->where('is_active', true);
    }

    /**
     * Người dùng đã giới thiệu tài khoản này
     */
    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    /**
     * Danh sách những người dùng do tài khoản này giới thiệu
     */
    public function referrals(): HasMany
    {
        return $this->hasMany(User::class, 'referrer_id');
    }

    /**
     * Lịch sử nhận hoa hồng / thưởng affiliate của tài khoản
     */
    public function affiliateHistories(): HasMany
    {
        return $this->hasMany(AffiliateHistory::class, 'user_id');
    }

    /**
     * Lấy số lượng thiết bị tối đa được phép đăng nhập theo gói cước hiện tại
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

        // 3. Fallback: Gói Free / Cơ Bản mặc định
        $normalPackage = Package::getNormalPackage();
        if ($normalPackage) {
            return (int) ($normalPackage->max_devices ?? 1);
        }

        return 1;
    }

    public function children(): HasMany
    {
        return $this->hasMany(Child::class, 'user_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'user_id');
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class, 'bank_id');
    }



    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'model_has_roles', 'model_id', 'role_id')
            ->withPivot('model_type')
            ->wherePivot('model_type', self::class);
    }


    public function checkPermissions($permissionsArr): bool
    {
        foreach ($permissionsArr as $permission) {
            if ($this->can($permission)) {
                return true;
            }
        }
        return false;
    }


    public function getJWTIdentifier()
    {
        return $this->getKey();
    }


    public function getJWTCustomClaims(): array
    {
        return [];
    }

    protected static function booted(): void
    {
        // Tự động sinh mã affiliate CC001, CC002... khi tạo người dùng mới
        static::creating(function ($user) {
            if (empty($user->affiliate_code)) {
                $user->affiliate_code = static::generateAffiliateCode();
            }
        });

        // Tao package trial
        static::created(function ($user) {
            $trialPackage = Package::getTrialPackage();
            $user->userPackages()->create([
                'package_id' => $trialPackage->id,
                'start_date' => now(),
                'end_date' => now()->addDays($trialPackage->days),
                'status' => PackageStatus::Active,
                'current_type' => PackageType::Trial
            ]);
        });
    }

    /**
     * Tự động sinh mã chia sẻ affiliate định dạng CC001, CC002...
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
     * Lấy tên hiển thị cấp bậc mẹ giới thiệu
     */
    public function getAffiliateRankName(): string
    {
        return $this->affiliate_rank?->name() ?? 'Mẹ Đồng';
    }

    /**
     * Lấy class badge hiển thị cấp bậc mẹ giới thiệu trên Admin
     */
    public function getAffiliateRankBadge(): string
    {
        return $this->affiliate_rank?->badge() ?? 'bg-orange-lt';
    }
}
