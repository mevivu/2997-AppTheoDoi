<?php

namespace App\Models;

use App\Admin\Support\Eloquent\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Enums\User\{ Gender, UserStatus};

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
        /** ID ngân hàng */
        'status',
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
        /** Loại địa chỉ */

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

    ];

    public function userPackages(): HasMany
    {
        return $this->hasMany(UserPackage::class);
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
        static::created(function ($user) {

        });
    }
}
