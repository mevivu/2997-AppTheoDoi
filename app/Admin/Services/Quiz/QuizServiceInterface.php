<?php

namespace App\Admin\Services\Quiz;
use Illuminate\Http\Request;

interface QuizServiceInterface
{


    public function store(Request $request);

    public function update(Request $request);

    public function delete($id);

    public function actionMultipleRecords(Request $request);



}
