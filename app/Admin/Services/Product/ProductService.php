<?php

namespace App\Admin\Services\Product;

use App\Admin\Repositories\Product\ProductRepositoryInterface;
use App\Admin\Traits\Roles;
use App\Api\V1\Support\UseLog;
use App\Enums\Product\ProductStatus;
use Exception;
use Illuminate\Http\Request;
use App\Admin\Traits\Setup;

class ProductService implements ProductServiceInterface
{
    use Setup, Roles, UseLog;

    /**
     * Current Object instance
     *
     * @var array
     */
    protected array $data;

    protected ProductRepositoryInterface $repository;

    public function __construct(
        ProductRepositoryInterface $repository,
    ) {
        $this->repository = $repository;
    }


    /**
     * @throws Exception
     */
    public function store(Request $request): object|false
    {
        $data = $request->validated();

        if (isset($data['product_catalog_id'])) {
            $productCatalogs = $data['product_catalog_id'];
            unset($data['product_catalog_id']);
        } else {
            $productCatalogs = [];
        }

        $product = $this->repository->create($data);

        if ($product) {
            $this->repository->syncProductCatalogs($product, $productCatalogs);

            return $product;
        }

        return false;
    }




    /**
     * @throws Exception
     */
    public function update(Request $request): object|bool
    {
        $data = $request->validated();

        if (isset($data['product_catalog_id'])) {
            $productCatalogs = $data['product_catalog_id'];
            unset($data['product_catalog_id']);
        } else {
            $productCatalogs = [];
        }

        $product = $this->repository->update($data['id'], $data);

        $this->repository->syncProductCatalogs($product, $productCatalogs);

        return true;
    }

    /**
     * @throws Exception
     */
    public function delete($id): object
    {
        return $this->repository->delete($id);
    }

    public function actionMultipleRecode(Request $request): bool
    {
        $this->data = $request->all();

        switch ($this->data['action']) {
            case 'active':
                foreach ($this->data['id'] as $value) {
                    $this->repository->updateAttribute($value, 'status', ProductStatus::Active);
                }
                return true;
            case 'inactive':
                foreach ($this->data['id'] as $value) {
                    $this->repository->updateAttribute($value, 'status', ProductStatus::Draft);
                }
                return true;
            case 'deleted':
                foreach ($this->data['id'] as $value) {
                    $this->repository->updateAttribute($value, 'status', ActiveStatus::Deleted);
                }
                return true;

            default:
                return false;
        }
    }
}
