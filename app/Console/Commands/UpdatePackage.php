<?php

namespace App\Console\Commands;

use App\Admin\Repositories\UserPackage\UserPackageRepositoryInterface;
use App\Enums\Package\PackageType;
use App\Models\Package;
use App\Traits\NotifiesViaFirebase;
use App\Traits\UseLog;
use Exception;
use Illuminate\Console\Command;

class UpdatePackage extends Command
{
    use NotifiesViaFirebase, UseLog;

    protected UserPackageRepositoryInterface $userPackageRepository;


    public function __construct(
        UserPackageRepositoryInterface $userPackageRepository,
    )
    {
        parent::__construct();
        $this->userPackageRepository = $userPackageRepository;

    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'package:update-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cập nhật gói dịch vụ user khi hết hạn';

    /**
     * Execute the console command.
     *
     * @return int
     * @throws Exception
     */
    public function handle(): int
    {
        try {
            $query = $this->userPackageRepository->getByQueryBuilder(
                [
                    ['current_type', '!=', PackageType::Normal]
                ]
            );
            $expiredPackages = $query->where('end_date', '<=', now())->get();
            $packageNormal = Package::getNormalPackage();
            foreach ($expiredPackages as $userPackage) {
                $this->userPackageRepository->update($userPackage->id, [
                    'current_type' => PackageType::Normal,
                    'package_id' => $packageNormal->id,
                    'end_date' => null,
                ]);
            }
            $this->logInfo('Update packages Normal successfully');

        } catch (Exception $e) {
            $this->error('Có lỗi xảy ra: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

}
