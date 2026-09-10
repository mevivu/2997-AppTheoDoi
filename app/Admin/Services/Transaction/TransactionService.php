<?php

namespace App\Admin\Services\Transaction;


use App\Admin\Repositories\Transaction\TransactionRepositoryInterface;
use App\Api\V1\Support\UseLog;
use App\Enums\DeleteStatus;
use App\Enums\Transaction\TransactionEnumService;
use App\Enums\Transaction\TransactionStatus;
use App\Enums\Transaction\TransactionType;
use App\Services\Affiliate\AffiliateServiceInterface;
use Exception;
use App\Admin\Traits\Setup;
use Throwable;

class TransactionService implements TransactionServiceInterface
{
    use Setup, UseLog;

    /**
     * Current Object instance
     *
     * @var array
     */
    protected array $data;

    protected TransactionRepositoryInterface $repository;


    public function __construct(
        TransactionRepositoryInterface $repository,
    )
    {
        $this->repository = $repository;
    }

    /**
     * @throws Exception
     */
    public function store($user, $package, $service = TransactionEnumService::NORMAL, $orderId = null, $purchaseToken = null): void
    {
        $amount = (float) (($package->price_discount ?? 0) > 0 ? $package->price_discount : ($package->price ?? 0));
        $data = [
            'user_id' => $user->id,
            'package_id' => $package->id,
            'code' => $this->createCodeTransaction(),
            'amount' => $amount,
            'type' => TransactionType::Payment,
            'status' => TransactionStatus::Confirmed,
            'google_order_id' => $orderId,
            'purchase_token' => $purchaseToken,
            'service' => $service,
            'is_deleted' => DeleteStatus::NotDeleted,
        ];
        $this->repository->create($data);

        try {
            app(AffiliateServiceInterface::class)->recordSalesAndCheckRankUpgrade(
                $user,
                (float) $data['amount'],
                $package->name ?? null
            );
        } catch (Throwable $e) {
            $this->logError('Lỗi cập nhật doanh số affiliate khi mua gói: ' . $e->getMessage(), $e);
        }
    }


}
