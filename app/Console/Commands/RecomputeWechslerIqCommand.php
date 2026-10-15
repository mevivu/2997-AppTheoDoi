<?php

namespace App\Console\Commands;

use App\Enums\Question\QuestionType;
use App\Models\IqNorm;
use App\Models\Rating;
use App\Services\Assessment\AssessmentBandResolver;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RecomputeWechslerIqCommand extends Command
{
    protected $signature = 'iq:recompute-wechsler {--dry-run : Chỉ hiển thị, không ghi vào database}';

    protected $description = 'Tính lại điểm IQ Wechsler của các bài test đã làm theo % câu đúng (result dạng "đúng/tổng")';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $updated = 0;
        $skipped = 0;

        Rating::where('type', QuestionType::IQ)
            ->where('score', '>', 15) // IQ chuẩn hóa có sàn 40; điểm <= 15 là thang cũ, giữ nguyên
            ->with('child')
            ->chunkById(200, function ($ratings) use ($dryRun, &$updated, &$skipped) {
                foreach ($ratings as $rating) {
                    $percent = IqNorm::percentFromResult($rating->result);
                    if ($percent === null) {
                        $skipped++;
                        continue;
                    }

                    $age = $rating->child?->birthday
                        ? Carbon::parse($rating->child->birthday)->diffInYears(Carbon::parse($rating->created_at ?? now()))
                        : (int) ($rating->age ?: 5);
                    $age = max(1, (int) $age);

                    $norm = IqNorm::getNormForAge($age);
                    $newIq = $norm
                        ? $norm->calculateIq($percent)
                        : IqNorm::fallbackIq($percent, 100);

                    if ((int) $rating->score === $newIq) {
                        continue;
                    }

                    $this->line("Rating #{$rating->id} ({$rating->result}, {$age} tuổi): {$rating->score} -> {$newIq}");

                    if (!$dryRun) {
                        $band = AssessmentBandResolver::resolve('iq', $newIq);
                        $rating->update([
                            'score' => $newIq,
                            'label' => $band['label'],
                            'description' => $band['description'],
                            'parent_action' => $band['parent_action'],
                        ]);
                    }
                    $updated++;
                }
            });

        $this->info(($dryRun ? '[DRY-RUN] ' : '') . "Đã cập nhật: {$updated}, bỏ qua (không có result hợp lệ): {$skipped}");

        return self::SUCCESS;
    }
}
