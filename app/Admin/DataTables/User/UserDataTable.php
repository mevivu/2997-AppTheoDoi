<?php

namespace App\Admin\DataTables\User;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\User\UserRepositoryInterface;
use App\Admin\Traits\Roles;
use App\AES\AESHelper;
use App\Enums\Package\PackageType;
use App\Enums\User\UserStatus;
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
            'email' => 'admin.users.datatable.email',
            'phone' => 'admin.users.datatable.phone',
            'package_type' => 'admin.users.datatable.package_type',
            'checkbox' => 'admin.common.checkbox',
        ];
    }

    public function setColumnSearch(): void
    {

        $this->columnAllSearch = [1, 2, 3, 4, 5, 6, 7];

        $this->columnSearchSelect = [
            [
                'column' => 5,
                'data' => UserStatus::asSelectArray()
            ],
            [
                'column' => 7,
                'data' => PackageType::asSelectArray()
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
            ->with(['roles', 'userPackages.package'])
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
            'package_type' => function ($item) {
                $type = optional($item->userPackages->first()?->package)->type;

                return view(
                    $this->view['package_type'],
                    [
                        'package_type' => $type ?? null
                    ]
                )->render();
            },
            'checkbox' => $this->view['checkbox'],
        ];
    }

    protected function setCustomRawColumns(): void
    {
        $this->customRawColumns = [
            'action',
            'status',
            'checkbox',
            'code',
            'email',
            'phone',
            'package_name',
            'package_type'
        ];
    }

    public function setCustomFilterColumns(): void
    {
        $this->customFilterColumns = [

            'package_type' => function ($query, $keyword) {
                $query->whereHas('userPackages', function ($subQuery) use ($keyword) {
                    $subQuery->where('current_type', 'like', '%' . $keyword . '%');
                });
            },
            'package_name' => function ($query, $keyword) {
                $query->whereHas('userPackages.package', function ($subQuery) use ($keyword) {
                    $subQuery->where('name', 'like', '%' . $keyword . '%');
                });
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

        if ($key === 'package_type') {
            $package = $row->userPackages->first()?->package;

            // Handle Native Enum (PHP 8.1+)
            if ($package && $package->type instanceof \BackedEnum && method_exists($package->type, 'description')) {
                return $package->type->description();
            }

            // Handle BenSampo Enum
            if ($package && $package->type instanceof Enum) {
                return $package->type->description;
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
