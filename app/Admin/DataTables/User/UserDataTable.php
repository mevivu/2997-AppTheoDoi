<?php

namespace App\Admin\DataTables\User;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\User\UserRepositoryInterface;
use App\Admin\Traits\Roles;
use App\AES\AESHelper;
use App\Enums\Package\PackageStatus;
use App\Enums\User\AffiliateRank;
use App\Enums\User\ParentRank;
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
        $cols = config('datatables_columns.user', []);
        $newCols = [];
        foreach ($cols as $key => $col) {
            $newCols[$key] = $col;
            if ($key === 'affiliate_rank') {
                $newCols['parent_rank'] = [
                    'title' => '<div class="header-cell-content" title="Điểm tổng hợp 4 tiêu chí: Thời gian (30%), Tần suất (25%), Bài đánh giá (25%), Chỉ số con (20%)"><i class="ti ti-crown text-warning me-1"></i><span>Hạng Bố mẹ</span></div>',
                    'addClass' => 'text-center align-middle',
                    'orderable' => true,
                ];
            }
        }
        $this->customColumns = !empty($newCols) ? $newCols : $cols;
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'code' => $this->view['editlink'],
            // Định dạng hiển thị số dư ví thưởng / hoa hồng Affiliate
            'wallet_balance' => function ($item) {
                $balance = (float) ($item->wallet_balance ?? 0);
                $amountClass = $balance > 0 ? 'text-success' : 'text-muted';
                return '<div class="wallet-cell"><span class="wallet-cell-icon"><i class="ti ti-wallet"></i></span><span><small>Ví hoa hồng</small><strong class="' . $amountClass . '">' . number_format($balance, 0, ',', '.') . ' đ</strong></span></div>';
            },
            // Định dạng hiển thị Cấp bậc mẹ giới thiệu
            'affiliate_rank' => function ($item) {
                $rank = $item->affiliate_rank ?? AffiliateRank::Silver;
                return '<div class="rank-cell"><span class="rank-cell-icon" style="color:' . $rank->colorHex() . ';background:' . $rank->colorHex() . '18"><i class="' . $rank->icon() . '"></i></span><span><small>Đối tác</small><strong>' . $rank->name() . '</strong></span></div>';
            },
            // Định dạng hiển thị Cấp bậc phân hạng Bố mẹ
            'parent_rank' => function ($item) {
                $rank = $item->parent_rank ?? ParentRank::NewMember;
                if (!$rank instanceof ParentRank) {
                    $rank = ParentRank::tryFrom((int) $rank) ?? ParentRank::NewMember;
                }
                $points = number_format((float) ($item->parent_rank_points ?? 0), 1);
                $period = $item->parent_rank_period ? 'Kỳ ' . $item->parent_rank_period : 'Kỳ hiện tại';
                $progress = min(100, max(0, (float) ($item->parent_rank_points ?? 0)));
                return '<div class="parent-rank-cell" title="' . $period . '"><div class="d-flex align-items-center justify-content-between gap-2"><span class="d-flex align-items-center gap-1 fw-semibold"><i class="' . $rank->icon() . '" style="color:' . $rank->colorHex() . '"></i>' . $rank->name() . '</span><strong>' . $points . 'đ</strong></div><div class="parent-rank-progress"><span style="width:' . $progress . '%;background:' . $rank->colorHex() . '"></span></div><small>' . $period . '</small></div>';
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
            'parent_rank',
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
            'parent_rank' => function ($query, $keyword) {
                $query->where('parent_rank', $keyword);
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
            $rank = $row->affiliate_rank ?? AffiliateRank::Silver;
            return $rank instanceof AffiliateRank ? $rank->name() : 'Bạc';
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
