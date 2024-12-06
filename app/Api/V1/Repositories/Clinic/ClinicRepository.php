<?php

namespace App\Api\V1\Repositories\Clinic;

use App\Admin\Repositories\Clinic\ClinicRepository as AdminRepository;




class ClinicRepository extends AdminRepository implements ClinicRepositoryInterface
{
    /**
     * Tìm kiếm phòng khám theo các tiêu chí.
     *
     * @param array $filters
     * @param int $limit
     * @param int $page
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */


    public function searchClinics(array $filters, int $limit = 10, int $page = 1)
    {
        // TODO: Implement searchClinics() method.
        $query=$this->model->query();
        if(!empty($filters['name'])){
            $query->where('name','like','%'.$filters['name'].'%');
        }
        if(!empty($filters['clinic_type_id'])){
            $query->where('clinic_type_id',$filters['clinic_type_id']);
        }
        if(!empty($filters['province_id'])){
            $query->where('province_id',$filters['province_id']);
        }
        if(!empty($filters['district_id'])){
            $query->where('district_id',$filters['district_id']);
        }
        if(!empty($filters['ward_id'])){
            $query->where('ward_id',$filters['ward_id']);
        }
        if(!empty($filters['opening_time'])){
            $query->where('opening_time', '>=', $filters['opening_time']);
        }
        return $query->paginate($limit, ['*'], 'page', $page);
    }
}
