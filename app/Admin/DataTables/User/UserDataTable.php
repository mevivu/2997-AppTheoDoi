<?php

namespace App\Admin\DataTables\User;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\User\UserRepositoryInterface;
use App\Admin\Traits\Roles;
use App\AES\AESHelper;
use App\Enums\Package\PackageStatus;
use App\Enums\User\AffiliateRank;
use App\Enums\User\UserServiceType;
use App\Enums\User\UserStatus;
use App\Models\Package;
use BenSampo\Enum\Enum;
use Illuminate\Database\Eloquent\Builder;
use Throwable;

class UserDataTable extends BaseDataTable
{
    use Roles;

    protected $nameTable = 'userTable';
    protected array $actions = ['reset', 'reload', 'excel'];

    public function __construct(
        UserRepositoryInterface $repository
    )
    {
        $this->repository = $repository;

        parent::__construct();
    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.users.datatable.action',
            'editlink' => 'admin.users.datatable.editlink',
            'status' => 'admin.users.datatable.status',
            'service_type' => 'admin.users.datatable.service_type',
            'email' => 'admin.users.datatable.email',
            'phone' => 'admin.users.datatable.phone',
            'checkbox' => 'admin.common.checkbox',
        ];
    }

    public function setColumnSearch(): void
    {
        $packages = Package::where(function ($q) {
            $q->whereIn('status', [PackageStatus::Active, PackageStatus::Draft])
                ->orWhereHas('userPackages');
        })
        ->orderBy('name')
        ->pluck('name', 'id')
        ->toArray();

        // Thứ tự cột: 0:checkbox (ẩn), 1:code, 2:fullname, 3:email, 4:phone, 5:wallet_balance, 6:affiliate_rank, 7:status, 8:package_name, 9:service_type, 10:action
        // Bật tìm kiếm cho các cột có dữ liệu tìm kiếm (Cột 5 Ví và 10 Thao tác để trống ô tìm kiếm)
        $this->columnAllSearch = [1, 2, 3, 4, 6, 7, 8, 9];

        $this->columnSearchSelect = [
            [
                'column' => 6,
                'data' => AffiliateRank::asSelectArray()
            ],
            [
                'column' => 7,
                'data' => UserStatus::asSelectArray()
            ],
            [
                'column' => 8,
                'data' => $packages
            ],
            [
                'column' => 9,
                'data' => UserServiceType::asSelectArray()
            ],
        ];
    }

    /**
     * Get query source of dataTable.
     *
     * @return Builder
     */
    public function query(): Builder
    {
        return $this->repository->getQueryBuilder()
            ->with([
                'roles',
                'referrer',
                'userPackages' => function ($q) {
                    $q->latest('id')->with('package');
                }
            ])
            ->withCount('referrals')
            ->orderByDesc('created_at');
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.user', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'code' => $this->view['editlink'],
            // Định dạng hiển thị số dư ví thưởng / hoa hồng Affiliate
            'wallet_balance' => function ($item) {
                $balance = (float) ($item->wallet_balance ?? 0);
                if ($balance > 0) {
                    return '<span class="badge bg-green-lt fw-bold font-monospace fs-4">' . number_format($balance, 0, ',', '.') . ' đ</span>';
                }
                return '<span class="text-muted font-monospace">0 đ</span>';
            },
            // Định dạng hiển thị Cấp bậc mẹ giới thiệu
            'affiliate_rank' => function ($item) {
                $rank = $item->affiliate_rank ?? AffiliateRank::Bronze;
                return '<span class="badge ' . $rank->badge() . '"><i class="' . $rank->icon() . ' me-1"></i>' . $rank->name() . '</span>';
            },
            'status' => $this->view['status'],
            'service_type' => $this->view['service_type'],
            'email' => function ($item) {
                return view(
                    $this->view['email'],
                    [
                        'email' => AESHelper::decrypt($item->email)
                    ]
                )->render();
            },
            'phone' => function ($item) {
                return view(
                    $this->view['phone'],
                    [
                        'phone' => $item->phone ? AESHelper::decrypt($item->phone) : null
                    ]
                )->render();
            },
        ];
    }

    protected function setCustomAddColumns(): void
    {
        $this->customAddColumns = [
            'action' => $this->view['action'],
            'package_name' => function ($item) {
                $name = optional($item->userPackages->first()?->package)->name;
                return $name ? '<span class="badge bg-green-lt">' . $name . '</span>' : '<span class="badge bg-secondary-lt">Chưa có</span>';
            },
            'checkbox' => $this->view['checkbox'],
        ];
    }

    protected function setCustomRawColumns(): void
    {
        $this->customRawColumns = [
            'action',
            'status',
            'service_type',
            'checkbox',
            'code',
            'wallet_balance',
            'affiliate_rank',
            'email',
            'phone',
            'package_name',
        ];
    }

    public function setCustomFilterColumns(): void
    {
        $this->customFilterColumns = [
            'affiliate_rank' => function ($query, $keyword) {
                $query->where('affiliate_rank', $keyword);
            },
            'status' => function ($query, $keyword) {
                $query->where('status', $keyword);
            },
            'service_type' => function ($query, $keyword) {
                $query->where('service_type', $keyword);
            },
            'package_name' => function ($query, $keyword) {
                if (is_numeric($keyword)) {
                    $query->whereHas('userPackages', function ($subQuery) use ($keyword) {
                        $subQuery->where('package_id', $keyword);
                    });
                } else {
                    $query->whereHas('userPackages.package', function ($subQuery) use ($keyword) {
                        $subQuery->where('name', 'like', '%' . $keyword . '%');
                    });
                }
            },
            'email' => function ($query, $keyword) {
                try {
                    $encrypted = AESHelper::encrypt($keyword);
                    $query->where('email', $encrypted);
                } catch (Throwable $e) {
                    $query->whereRaw('0 = 1');
                }
            },
            'phone' => function ($query, $keyword) {
                try {
                    $encrypted = AESHelper::encrypt($keyword);
                    $query->where('phone', $encrypted);
                } catch (Throwable $e) {
                    $query->whereRaw('0 = 1');
                }
            },
        ];
    }
    protected function getExportValue($key, $row)
    {
        if ($key === 'wallet_balance') {
            return number_format($row->wallet_balance ?? 0, 0, ',', '.') . ' đ';
        }

        if ($key === 'affiliate_rank') {
            $rank = $row->affiliate_rank ?? AffiliateRank::Bronze;
            return $rank instanceof AffiliateRank ? $rank->name() : 'Mẹ Đồng';
        }

        if ($key === 'package_name') {
            return optional($row->userPackages->first()?->package)->name ?? '';
        }

        if ($key === 'service_type') {
            if ($row->service_type instanceof UserServiceType) {
                return $row->service_type->name();
            }
            if (is_numeric($row->service_type)) {
                return UserServiceType::tryFrom((int)$row->service_type)?->name() ?? $row->service_type;
            }
            return '';
        }

        if ($key === 'status') {
            // Check if object is already Enum
            if ($row->status instanceof \BackedEnum && method_exists($row->status, 'description')) {
                return $row->status->description();
            }
            // Fallback for raw integer value
            if (is_numeric($row->status)) {
                return UserStatus::tryFrom($row->status)?->description() ?? $row->status;
            }
        }

        if (in_array($key, ['email', 'phone'])) {
            $value = $row->{$key} ?? '';
            if (!empty($value)) {
                $decrypted = AESHelper::decrypt($value);
                if ($decrypted) {
                    return $decrypted;
                }
            }
        }

        return parent::getExportValue($key, $row);
    }
}
