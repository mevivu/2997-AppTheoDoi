<?php

namespace App\Admin\Http\Controllers\Address;

use App\Admin\Exel\Address\Province\ProvinceExport;
use App\Admin\Exel\Address\Ward\WardExport;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Repositories\Province\ProvinceRepositoryInterface;
use App\Admin\Repositories\Ward\WardRepositoryInterface;
use App\Traits\MessageSystem;
use App\Traits\UseLog;
use Exception;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;


class AddressController extends Controller
{
    use UseLog;

    protected ProvinceRepositoryInterface $provinceRepository;

    protected WardRepositoryInterface $wardRepository;

    public function __construct(
        ProvinceRepositoryInterface $provinceRepository,
        WardRepositoryInterface     $wardRepository
    )
    {
        parent::__construct();
        $this->provinceRepository = $provinceRepository;
        $this->wardRepository = $wardRepository;
    }


    public function exportProvince(): BinaryFileResponse
    {
        try {
            $provinces = $this->provinceRepository->getAll();

            return Excel::download(new ProvinceExport($provinces), 'danh_sach_tinh_' . date('Y-m-d_H-i-s') . '.xlsx');

        } catch (Exception $e) {
            $this->logError(MessageSystem::SERVER_ERROR, $e);
            abort(500, MessageSystem::SERVER_ERROR);
        }
    }

    public function exportWard(): BinaryFileResponse
    {
        try {
            $wards = $this->wardRepository->getAll();

            return Excel::download(new WardExport($wards), 'danh_sach_phuong_xa_' . date('Y-m-d_H-i-s') . '.xlsx');

        } catch (Exception $e) {
            $this->logError(MessageSystem::SERVER_ERROR, $e);
            abort(500, MessageSystem::SERVER_ERROR);
        }
    }


}
