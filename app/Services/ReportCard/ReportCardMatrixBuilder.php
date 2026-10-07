<?php

namespace App\Services\ReportCard;

use App\Enums\Class\EducationLevel;
use App\Enums\Semester\SemesterStatus;
use App\Models\ClassGrade;
use App\Models\SchoolClass;
use Illuminate\Support\Collection;

class ReportCardMatrixBuilder
{
    /**
     * Build the summary matrix for a given education level stage.
     *
     * @param string $educationLevel EducationLevel value (primary, lower_secondary, upper_secondary)
     * @param Collection $classGrades Collection of ClassGrade for this child, keyed by class_id
     * @param Collection $allClasses Collection of SchoolClass with relations
     * @return array
     */
    public function buildStageMatrix(
        string $educationLevel,
        Collection $classGrades,
        Collection $allClasses
    ): array {
        $stageConfig = config("report_card_matrix.stages.{$educationLevel}");
        if (!$stageConfig) {
            return [];
        }

        $classIds = $stageConfig['class_ids'] ?? [];
        $columns = [];

        foreach ($classIds as $cid) {
            $cg = $classGrades->get($cid);
            $cls = $allClasses->get($cid);
            $className = $cls?->name ?? "Lớp {$cid}";

            $fullYearEval = $cg?->evaluations->first(function ($eval) {
                $sem = $eval->semester instanceof \BackedEnum ? $eval->semester->value : (string) $eval->semester;
                return $sem === SemesterStatus::FullYear->value;
            });

            $columns[] = [
                'class_id' => $cid,
                'name' => $className,
                'has_class_grade' => $cg !== null,
                'has_full_year' => $fullYearEval !== null,
            ];
        }

        $sections = [];

        // 1. Subjects Section
        $subjectRows = [];
        foreach ($stageConfig['subjects'] ?? [] as $subDef) {
            $cells = [];
            foreach ($classIds as $cid) {
                $cg = $classGrades->get($cid);
                $cls = $allClasses->get($cid);
                $cells[(string) $cid] = $this->buildSubjectCell($educationLevel, $subDef, $cid, $cls, $cg);
            }

            $subjectRows[] = [
                'key' => $subDef['key'],
                'label' => $subDef['label'],
                'is_composite' => (bool) ($subDef['is_composite'] ?? false),
                'cells' => $cells,
            ];
        }

        $sections[] = [
            'key' => 'subjects',
            'label' => '1. MÔN HỌC',
            'rows' => $subjectRows,
        ];

        // 2. Qualities Section
        $qualityDefs = config('report_card_matrix.qualities', []);
        $qualityRows = [];
        foreach ($qualityDefs as $qDef) {
            $cells = [];
            foreach ($classIds as $cid) {
                $cg = $classGrades->get($cid);
                $cells[(string) $cid] = $this->buildQualityCell($qDef, $cg);
            }
            $qualityRows[] = [
                'id' => $qDef['id'],
                'key' => $qDef['key'],
                'label' => $qDef['label'],
                'cells' => $cells,
            ];
        }

        $sections[] = [
            'key' => 'qualities',
            'label' => '2. PHẨM CHẤT',
            'rows' => $qualityRows,
        ];

        // 3. Capabilities Section
        $capDefs = config('report_card_matrix.capabilities', []);
        $capRows = [];
        foreach ($capDefs as $cDef) {
            $cells = [];
            foreach ($classIds as $cid) {
                $cg = $classGrades->get($cid);
                $cells[(string) $cid] = $this->buildCapabilityCell($cDef, $cg);
            }
            $capRows[] = [
                'id' => $cDef['id'],
                'key' => $cDef['key'],
                'label' => $cDef['label'],
                'cells' => $cells,
            ];
        }

        $sections[] = [
            'key' => 'capabilities',
            'label' => '3. NĂNG LỰC',
            'rows' => $capRows,
        ];

        return [
            'education_level' => $educationLevel,
            'label' => $stageConfig['label'] ?? '',
            'columns' => $columns,
            'sections' => $sections,
            'legend' => $stageConfig['legend'] ?? [],
        ];
    }

    /**
     * Build subject cell data.
     */
    protected function buildSubjectCell(
        string $educationLevel,
        array $subDef,
        int $classId,
        ?SchoolClass $cls,
        ?ClassGrade $cg
    ): array {
        if (!$cls) {
            return [
                'state' => 'not_applicable',
                'display' => '-',
            ];
        }

        // Find configured subjects in this class that match match_names
        $matchedConfigured = $cls->subjects->filter(function ($s) use ($subDef) {
            return in_array($s->name, $subDef['match_names'] ?? [], true);
        });

        if ($matchedConfigured->isEmpty()) {
            return [
                'state' => 'not_applicable',
                'display' => '-',
            ];
        }

        if (!$cg) {
            return [
                'state' => 'empty',
                'display' => '--',
            ];
        }

        // Check full year first, fallback to semester 2 if full year has no grades
        $eval = $cg->evaluations->first(function ($e) {
            $sem = $e->semester instanceof \BackedEnum ? $e->semester->value : (string) $e->semester;
            return $sem === SemesterStatus::FullYear->value;
        });

        if (!$eval || $eval->subjectGrades->isEmpty()) {
            $sem2 = $cg->evaluations->first(function ($e) {
                $sem = $e->semester instanceof \BackedEnum ? $e->semester->value : (string) $e->semester;
                return $sem === SemesterStatus::Semester2->value;
            });
            if ($sem2 && $sem2->subjectGrades->isNotEmpty()) {
                $eval = $sem2;
            }
        }

        if (!$eval) {
            return [
                'state' => 'empty',
                'display' => '--',
            ];
        }

        $subjectGrades = $eval->subjectGrades->keyBy('subject_id');
        $tokens = [];
        $hasAnyValue = false;
        $details = [];

        foreach ($matchedConfigured as $sub) {
            $sg = $subjectGrades->get($sub->id);
            $pivot = $sub->pivot;
            $method = $pivot?->evaluation_method ?? 'score';

            $token = null;
            $rawScore = $sg?->full_year_grade ?? $sg?->grade;
            $level = $sg?->achievement_level instanceof \BackedEnum
                ? $sg->achievement_level->value
                : $sg?->achievement_level;

            if ($educationLevel === EducationLevel::Primary->value) {
                $levelCode = $this->mapPrimaryLevelCode($level);
                $scoreStr = $rawScore !== null ? $this->formatScore((float) $rawScore) : null;

                if ($levelCode !== null && $scoreStr !== null) {
                    $token = "{$levelCode} · {$scoreStr}";
                    $hasAnyValue = true;
                } elseif ($scoreStr !== null) {
                    $token = $scoreStr;
                    $hasAnyValue = true;
                } elseif ($levelCode !== null) {
                    $token = $levelCode;
                    $hasAnyValue = true;
                }
            } else {
                // Secondary (THCS / THPT)
                if ($method === 'comment') {
                    if ($level) {
                        $token = $this->mapSecondaryCommentCode($level);
                        $hasAnyValue = true;
                    }
                } else {
                    if ($rawScore !== null) {
                        $token = $this->formatScore((float) $rawScore);
                        $hasAnyValue = true;
                    }
                }
            }

            if ($token !== null) {
                $tokens[] = $token;
            } else {
                $tokens[] = '--';
            }

            $details[] = [
                'subject_id' => $sub->id,
                'name' => $sub->name,
                'method' => $method,
                'grade' => $rawScore !== null ? (float) $rawScore : null,
                'achievement_level' => $level,
                'token' => $token,
            ];
        }

        if (!$hasAnyValue) {
            return [
                'state' => 'empty',
                'display' => '--',
            ];
        }

        return [
            'state' => 'filled',
            'display' => implode(' / ', $tokens),
            'details' => $details,
        ];
    }

    /**
     * Build quality cell data.
     */
    protected function buildQualityCell(array $qDef, ?ClassGrade $cg): array
    {
        if (!$cg) {
            return [
                'state' => 'empty',
                'display' => '--',
            ];
        }

        $eval = $cg->evaluations->first(function ($e) {
            $sem = $e->semester instanceof \BackedEnum ? $e->semester->value : (string) $e->semester;
            return $sem === SemesterStatus::FullYear->value;
        }) ?? $cg->evaluations->first(function ($e) {
            $sem = $e->semester instanceof \BackedEnum ? $e->semester->value : (string) $e->semester;
            return $sem === SemesterStatus::Semester2->value;
        });

        if (!$eval || $eval->qualities->isEmpty()) {
            return [
                'state' => 'empty',
                'display' => '--',
            ];
        }

        $cq = $eval->qualities->first(function ($item) use ($qDef) {
            if ($item->quality_id === $qDef['id']) {
                return true;
            }
            $name = $item->quality?->name;
            return $name && in_array($name, $qDef['match_names'] ?? [], true);
        });

        $status = $cq?->quality_status instanceof \BackedEnum
            ? $cq->quality_status->value
            : $cq?->quality_status;

        if (!$status) {
            return [
                'state' => 'empty',
                'display' => '--',
            ];
        }

        return [
            'state' => 'filled',
            'display' => $this->mapQualityCapabilityCode($status),
            'status' => $status,
        ];
    }

    /**
     * Build capability cell data.
     */
    protected function buildCapabilityCell(array $cDef, ?ClassGrade $cg): array
    {
        if (!$cg) {
            return [
                'state' => 'empty',
                'display' => '--',
            ];
        }

        $eval = $cg->evaluations->first(function ($e) {
            $sem = $e->semester instanceof \BackedEnum ? $e->semester->value : (string) $e->semester;
            return $sem === SemesterStatus::FullYear->value;
        }) ?? $cg->evaluations->first(function ($e) {
            $sem = $e->semester instanceof \BackedEnum ? $e->semester->value : (string) $e->semester;
            return $sem === SemesterStatus::Semester2->value;
        });

        if (!$eval || $eval->capabilities->isEmpty()) {
            return [
                'state' => 'empty',
                'display' => '--',
            ];
        }

        $cc = $eval->capabilities->first(function ($item) use ($cDef) {
            if ($item->capability_id === $cDef['id']) {
                return true;
            }
            $name = $item->capability?->name;
            return $name && in_array($name, $cDef['match_names'] ?? [], true);
        });

        $status = $cc?->capability_status instanceof \BackedEnum
            ? $cc->capability_status->value
            : $cc?->capability_status;

        if (!$status) {
            return [
                'state' => 'empty',
                'display' => '--',
            ];
        }

        return [
            'state' => 'filled',
            'display' => $this->mapQualityCapabilityCode($status),
            'status' => $status,
        ];
    }

    protected function mapPrimaryLevelCode(?string $level): ?string
    {
        if (!$level) {
            return null;
        }

        return match ($level) {
            'excellent', 'good' => 'T',
            'completed', 'completed_good', 'achieved' => 'H',
            'not_completed', 'not_achieved' => 'C',
            default => $level,
        };
    }

    protected function mapSecondaryCommentCode(?string $level): string
    {
        if (!$level) {
            return '--';
        }

        return match ($level) {
            'achieved', 'good', 'completed', 'completed_good' => 'Đ',
            'not_achieved', 'not_completed' => 'CĐ',
            default => $level,
        };
    }

    protected function mapQualityCapabilityCode(?string $status): string
    {
        if (!$status) {
            return '--';
        }

        return match ($status) {
            'good' => 'T',
            'achieved' => 'Đ',
            'not_achieved' => 'C',
            default => $status,
        };
    }

    protected function formatScore(float $score): string
    {
        if ((int) $score == $score) {
            return (string) (int) $score;
        }

        return number_format($score, 1);
    }
}
