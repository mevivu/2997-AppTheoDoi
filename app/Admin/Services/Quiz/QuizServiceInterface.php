<?php

namespace App\Admin\Services\Quiz;
use Illuminate\Http\Request;

interface QuizServiceInterface
{

    public function checkTypeExists(array $types): bool;
    public function store(Request $request);

    public function storeIQ(Request $request);

    public function update(Request $request);

    public function updateIQ(Request $request);

    public function delete($id);

    public function actionMultipleRecords(Request $request);



}
