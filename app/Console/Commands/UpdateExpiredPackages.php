<?php

namespace App\Console\Commands;

use App\Admin\Repositories\UserPackage\UserPackageRepositoryInterface;
use App\Enums\Package\PackageType;
use App\Models\Package;
use App\Traits\UseLog;
use Exception;
use Illuminate\Console\Command;

class UpdateExpiredPackages extends Command
{
    use UseLog;

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
    protected $description = 'Updates expired packages to a normal package type';

    protected UserPackageRepositoryInterface $userPackageRepository;


    public function __construct(
        UserPackageRepositoryInterface $userPackageRepository,
    )
    {
        parent::__construct();
        $this->userPackageRepository = $userPackageRepository;

    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        try {
            $query = $this->userPackageRepository->getByQueryBuilder([['current_type', '!=', PackageType::Normal]]);
            $expiredPackages = $query->where('end_date', '<=', now())->get();
            $packageNormal = Package::getNormalPackage();
            foreach ($expiredPackages as $userPackage) {
                $result = $userPackage->update([
                    'current_type' => PackageType::Normal,
                    'package_id' => $packageNormal->id,
                ]);
                if ($result) {
                    $this->logInfo("Updated package {$userPackage->id} successfully.");
                } else {
                    $this->logInfo("Error: Failed to update package {$userPackage->id}.");
                }
            }
            $this->logInfo('Update packages Normal successfully');
        } catch (Exception $e) {
            $this->error('Có lỗi xảy ra: ' . $e->getMessage());
            return Command::FAILURE;
        }
        return Command::SUCCESS;
    }
}
