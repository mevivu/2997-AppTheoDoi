<?php

namespace App\Console\Commands;

use App\Enums\ReportCard\CalculationStatus;
use App\Models\ClassGrade;
use App\Services\ReportCard\Contracts\ReportCardCalculator;
use App\Services\ReportCard\ReportCardEngine;
use App\Services\ReportCard\ReportCardInputLoader;
use App\Services\ReportCard\ReportCardPersister;
use App\Traits\UseLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

class RecalculateReportCardCommand extends Command
{
    use UseLog;

    /**
     * Tên và cú pháp gọi command
     *
     * @var string
     */
    protected $signature = 'report-card:recalculate
        {--dry-run : Chế độ chạy thử, chỉ in diff và không lưu DB}
        {--child= : ID của trẻ cụ thể cần tính lại}
        {--class-grade= : ID của bảng điểm lớp cụ thể}
        {--level= : Lọc theo cấp học (primary, lower_secondary, upper_secondary)}
        {--chunk=200 : Số lượng bản ghi xử lý trong mỗi lượt}
        {--sleep=0 : Thời gian nghỉ giữa các chunk (milliseconds)}
        {--write-calculated : Ghi nhận kết quả tính toán calculated_* và snapshot vào DB}
        {--apply-final : Áp dụng kết quả vào academic_performance chính thức}
        {--force : Ghi đè cả những bản ghi phụ huynh đã chủ động override}
        {--fix-full-year-precision : Tính lại điểm cả năm và lưu với độ chính xác 2 chữ số thập phân}
        {--export-csv : Xuất báo cáo audit log dạng CSV ra storage/logs}';

    /**
     * Mô tả chức năng của command
     *
     * @var string
     */
    protected $description = 'Tính toán lại học lực học bạ theo Thông tư 27 (Tiểu học) và Thông tư 22 (THCS/THPT)';

    protected ReportCardInputLoader $loader;
    protected ReportCardPersister $persister;
    protected ReportCardEngine $engine;

    public function __construct(
        ReportCardInputLoader $loader,
        ReportCardPersister   $persister,
        ReportCardEngine      $engine
    ) {
        parent::__construct();
        $this->loader = $loader;
        $this->persister = $persister;
        $this->engine = $engine;
    }

    /**
     * Thực thi lệnh
     *
     * @return int
     */
    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $writeCalculated = (bool) $this->option('write-calculated');
        $applyFinal = (bool) $this->option('apply-final');
        $force = (bool) $this->option('force');
        $fixFullYearPrecision = (bool) $this->option('fix-full-year-precision');
        $exportCsv = (bool) $this->option('export-csv');
        $chunkSize = max(1, (int) $this->option('chunk'));
        $sleepMs = max(0, (int) $this->option('sleep'));

        // Nếu không chỉ định ghi, mặc định là dry-run an toàn
        if (!$writeCalculated && !$applyFinal && !$fixFullYearPrecision) {
            $dryRun = true;
        }

        $this->info('=== BẮT ĐẦU TÍNH TOÁN LẠI HỌC BẠ ĐIỆN TỬ ===');
        $this->line('Chế độ: ' . ($dryRun ? '<comment>DRY-RUN (Chỉ đọc, không ghi DB)</comment>' : '<fg=green>WRITE (Ghi vào DB)</fg=green>'));
        if ($writeCalculated) {
            $this->line('- Ghi nhận calculated_academic_performance & calculation_snapshot: <info>BẬT</info>');
        }
        if ($applyFinal) {
            $this->line('- Áp dụng vào academic_performance chính thức: <info>BẬT</info>' . ($force ? ' (Bao gồm cả bản ghi override)' : ' (Bỏ qua bản ghi override)'));
        }

        $query = ClassGrade::query()
            ->whereHas('evaluations')
            ->with(['class', 'evaluations.subjectGrades.subject'])
            ->orderBy('id');

        if ($childId = $this->option('child')) {
            $query->where('child_id', $childId);
        }

        if ($classGradeId = $this->option('class-grade')) {
            $query->where('id', $classGradeId);
        }

        if ($level = $this->option('level')) {
            $query->whereHas('class', function ($q) use ($level) {
                $q->where('education_level', $level)->orWhere('level_group', $level);
            });
        }

        $totalClassGrades = $query->count();
        if ($totalClassGrades === 0) {
            $this->warn('Không tìm thấy bảng điểm nào phù hợp điều kiện.');
            return Command::SUCCESS;
        }

        $this->line("Tìm thấy <info>{$totalClassGrades}</info> bảng điểm lớp cần duyệt.");

        // Chuẩn bị CSV nếu yêu cầu
        $csvFile = null;
        $csvPath = null;
        if ($exportCsv) {
            $csvDir = storage_path('logs');
            if (!is_dir($csvDir)) {
                mkdir($csvDir, 0755, true);
            }
            $timestamp = date('Ymd_His');
            $csvPath = "{$csvDir}/report_card_backfill_{$timestamp}.csv";
            $csvFile = fopen($csvPath, 'w');
            fputcsv($csvFile, [
                'evaluation_id',
                'class_grade_id',
                'class_id',
                'semester',
                'old_ap',
                'calculated_ap',
                'status',
                'will_change',
                'is_overridden',
                'reason',
            ]);
        }

        $stats = [
            'total_evaluations' => 0,
            'status_ok' => 0,
            'status_incomplete' => 0,
            'status_invalid' => 0,
            'will_change' => 0,
            'unchanged' => 0,
            'overridden_skipped' => 0,
        ];

        $progressBar = $this->output->createProgressBar($totalClassGrades);
        $progressBar->start();

        $query->chunk($chunkSize, function ($classGrades) use (
            &$stats,
            $dryRun,
            $writeCalculated,
            $applyFinal,
            $force,
            $fixFullYearPrecision,
            $csvFile,
            $sleepMs,
            $progressBar
        ) {
            foreach ($classGrades as $classGrade) {
                try {
                    $evaluations = $classGrade->evaluations;

                    foreach ($evaluations as $eval) {
                        $semKey = $eval->semester instanceof \BackedEnum ? $eval->semester->value : (string) $eval->semester;
                        $input = $this->loader->load($classGrade, $semKey);
                        $calcResult = $this->engine->calculate($input);

                        $stats['total_evaluations']++;

                        $status = $calcResult->status;
                        if ($status === 'ok') {
                            $stats['status_ok']++;
                        } elseif ($status === 'incomplete') {
                            $stats['status_incomplete']++;
                        } else {
                            $stats['status_invalid']++;
                        }

                        $currentAp = $eval->academic_performance instanceof \BackedEnum
                            ? $eval->academic_performance->value
                            : (string) $eval->academic_performance;

                        $calculatedAp = $calcResult->rating;
                        $isOverridden = (bool) $eval->is_performance_overridden;
                        $willChange = false;
                        $reason = '';

                        if ($calculatedAp && $calculatedAp !== $currentAp) {
                            if ($isOverridden && !$force) {
                                $stats['overridden_skipped']++;
                                $reason = 'Bỏ qua do người dùng đã override';
                            } else {
                                $willChange = true;
                                $stats['will_change']++;
                                $reason = "Thay đổi: {$currentAp} -> {$calculatedAp}";
                            }
                        } else {
                            $stats['unchanged']++;
                            $reason = 'Giữ nguyên';
                        }

                        if ($csvFile) {
                            fputcsv($csvFile, [
                                $eval->id,
                                $classGrade->id,
                                $classGrade->class_id,
                                $semKey,
                                $currentAp,
                                $calculatedAp,
                                $status,
                                $willChange ? 1 : 0,
                                $isOverridden ? 1 : 0,
                                $reason,
                            ]);
                        }

                        // Lưu vào DB nếu không phải dry-run
                        if (!$dryRun && ($writeCalculated || $applyFinal)) {
                            DB::transaction(function () use ($classGrade, $semKey, $calcResult, $input, $eval, $applyFinal, $force) {
                                if ($applyFinal && $force) {
                                    $eval->is_performance_overridden = false;
                                }

                                \Illuminate\Support\Facades\Config::set('report_card.auto_classification', $applyFinal);
                                $this->persister->persist($classGrade, $semKey, $calcResult, $input);
                            });
                        }
                    }

                    // Sửa làm tròn điểm cả năm nếu có cờ fix-full-year-precision
                    if (!$dryRun && $fixFullYearPrecision) {
                        if ($classGrade->semester1_grade !== null && $classGrade->semester2_grade !== null) {
                            $s1 = (float) $classGrade->semester1_grade;
                            $s2 = (float) $classGrade->semester2_grade;
                            $fy = round(($s1 + 2 * $s2) / 3, 2);
                            $classGrade->update(['full_year_grade' => $fy]);
                        }
                    }
                } catch (Throwable $e) {
                    $this->logError('Error recalculating class grade ' . $classGrade->id, $e);
                }

                $progressBar->advance();
            }

            if ($sleepMs > 0) {
                usleep($sleepMs * 1000);
            }
        });

        $progressBar->finish();
        $this->newLine(2);

        if ($csvFile) {
            fclose($csvFile);
            $this->info("Báo cáo audit log CSV đã được xuất ra: <comment>{$csvPath}</comment>");
        }

        // Bảng thống kê kết quả
        $this->table(
            ['Chỉ số thống kê', 'Số lượng bản ghi'],
            [
                ['Tổng số học kỳ đánh giá đã duyệt', number_format($stats['total_evaluations'])],
                ['Tính toán thành công (OK)', number_format($stats['status_ok'])],
                ['Chưa đủ dữ liệu môn học (Incomplete)', number_format($stats['status_incomplete'])],
                ['Dữ liệu điểm không hợp lệ (Invalid Input)', number_format($stats['status_invalid'])],
                ['Xếp loại sẽ thay đổi (Will Change)', number_format($stats['will_change'])],
                ['Xếp loại giữ nguyên (Unchanged)', number_format($stats['unchanged'])],
                ['Bỏ qua do cờ ghi đè (Overridden Skipped)', number_format($stats['overridden_skipped'])],
            ]
        );

        $this->info('=== HOÀN TẤT QUÁ TRÌNH TÍNH TOÁN LẠI HỌC BẠ ===');
        return Command::SUCCESS;
    }
}
