<?php

namespace App\Admin\DataTables\User;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\User\UserRepositoryInterface;
use App\Admin\Traits\Roles;
use App\AES\AESHelper;
use App\Enums\Package\PackageStatus;
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

        $this->columnAllSearch = [1, 2, 3, 4, 5, 6, 7];

        $this->columnSearchSelect = [
            [
                'column' => 5,
                'data' => UserStatus::asSelectArray()
            ],
            [
                'column' => 6,
                'data' => $packages
            ],
            [
                'column' => 7,
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
            'email',
            'phone',
            'package_name',
        ];
    }

    public function setCustomFilterColumns(): void
    {
        $this->customFilterColumns = [
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
