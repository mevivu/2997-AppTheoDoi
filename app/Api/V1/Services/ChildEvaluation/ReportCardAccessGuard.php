<?php

namespace App\Api\V1\Services\ChildEvaluation;

use App\Api\V1\Exception\ReportCardAccessDeniedException;
use App\Api\V1\Support\AuthServiceApi;
use Illuminate\Support\Facades\DB;

/**
 * Kiểm tra quyền sở hữu dữ liệu học bạ.
 *
 * Mọi endpoint học bạ (child-evaluations) phải đi qua guard này để đảm bảo
 * user chỉ đọc/sửa được học bạ của trẻ thuộc chính tài khoản mình (chống IDOR).
 */
class ReportCardAccessGuard
{
    use AuthServiceApi;

    /**
     * @throws ReportCardAccessDeniedException
     */
    public function assertOwnsChild(int|string|null $childId): void
    {
        $userId = $this->getCurrentUserId();

        if (!$userId || !$childId) {
            throw new ReportCardAccessDeniedException();
        }

        $owned = DB::table('children')
            ->where('id', $childId)
            ->where('user_id', $userId)
            ->exists();

        if (!$owned) {
            throw new ReportCardAccessDeniedException();
        }
    }

    /**
     * @throws ReportCardAccessDeniedException
     */
    public function assertOwnsEvaluation(int|string|null $childEvaluationId): void
    {
        $userId = $this->getCurrentUserId();

        if (!$userId || !$childEvaluationId) {
            throw new ReportCardAccessDeniedException();
        }

        $owned = DB::table('child_evaluations as ce')
            ->join('class_grades as cg', 'cg.id', '=', 'ce.class_grade_id')
            ->join('children as c', 'c.id', '=', 'cg.child_id')
            ->where('ce.id', $childEvaluationId)
            ->where('c.user_id', $userId)
            ->exists();

        if (!$owned) {
            throw new ReportCardAccessDeniedException();
        }
    }

    /**
     * @throws ReportCardAccessDeniedException
     */
    public function assertOwnsAttachment(int|string|null $attachmentId): void
    {
        $userId = $this->getCurrentUserId();

        if (!$userId || !$attachmentId) {
            throw new ReportCardAccessDeniedException();
        }

        $owned = DB::table('child_evaluation_attachments as cea')
            ->join('child_evaluations as ce', 'ce.id', '=', 'cea.child_evaluation_id')
            ->join('class_grades as cg', 'cg.id', '=', 'ce.class_grade_id')
            ->join('children as c', 'c.id', '=', 'cg.child_id')
            ->where('cea.id', $attachmentId)
            ->where('c.user_id', $userId)
            ->exists();

        if (!$owned) {
            throw new ReportCardAccessDeniedException();
        }
    }
}
