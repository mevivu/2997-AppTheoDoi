<?php

namespace App\Admin\Repositories\Quiz;

use App\Admin\Repositories\EloquentRepository;
use App\Models\Quiz;

class QuizRepository extends EloquentRepository implements QuizRepositoryInterface
{
    public function getModel(): string
    {
        return Quiz::class;
    }
    public function existsWithTypes(array $types): bool
    {
        return Quiz::whereIn('type', $types)->exists();
    }
}
