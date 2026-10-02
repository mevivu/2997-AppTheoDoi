<?php

namespace App\Admin\Services\KnowledgePost;

use Illuminate\Http\Request;

interface KnowledgePostServiceInterface
{
    /**
     * Tạo mới
     * 
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request);

    /**
     * Cập nhật
     * 
     * @param Request $request
     * @return boolean|object
     */
    public function update(Request $request);

    /**
     * Xóa
     * 
     * @param mixed $id
     * @return boolean|object
     */
    public function delete($id);

    public function actionMultipleRecode(Request $request): bool;
}
