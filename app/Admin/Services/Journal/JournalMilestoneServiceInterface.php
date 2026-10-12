<?php

namespace App\Admin\Services\Journal;

use Illuminate\Http\Request;

interface JournalMilestoneServiceInterface
{
    public function store(Request $request): object|false;

    public function update(Request $request): object|bool;

    public function delete($id): bool;
}
