<?php

namespace App\Services\ReportCard;

use App\Enums\Class\EducationLevel;
use App\Enums\Semester\SemesterStatus;
use App\Models\ClassGrade;
use App\Services\ReportCard\Support\RatingLadder;

class ReportCardSummaryService
{
    public function getSummary(int $childId): array
    {
        $classGrades = ClassGrade::query()
            ->where('child_id', $childId)
            ->with([
                'class',
                'evaluations' => fn($q) => $q->withCount('attachments')->with('subjectGrades.subject'),
            ])
            ->get()
            ->keyBy('class_id');

        $stageDefinitions = [
            EducationLevel::Primary->value => [
                'label' => 'Tiểu học',
                'class_ids' => [1, 2, 3, 4, 5],
            ],
            EducationLevel::LowerSecondary->value => [
                'label' => 'THCS',
                'class_ids' => [6, 7, 8, 9],
            ],
            EducationLevel::UpperSecondary->value => [
                'label' => 'THPT',
                'class_ids' => [10, 11, 12],
            ],
        ];

        $stages = [];
        $subjectScoresAccumulator = []; // subject_id => ['name' => ..., 'scores' => []]

        foreach ($stageDefinitions as $level => $def) {
            $classDataList = [];
            $fullYearCount = 0;
            $ratingDist = [];
            $averageTrend = [];
            $latestRating = null;

            foreach ($def['class_ids'] as $cid) {
                $cg = $classGrades->get($cid);
                $className = $cg?->class?->name ?? "Lớp {$cid}";

                $semestersData = [
                    SemesterStatus::Semester1->value => null,
                    SemesterStatus::Semester2->value => null,
                    SemesterStatus::FullYear->value => null,
                ];
                $totalAttachments = 0;

                if ($cg) {
                    foreach ($cg->evaluations as $eval) {
                        $sem = $eval->semester instanceof \BackedEnum ? $eval->semester->value : (string) $eval->semester;
                        $totalAttachments += (int) ($eval->attachments_count ?? 0);

                        $semestersData[$sem] = [
                            'academic_performance' => $eval->academic_performance?->value ?? $eval->academic_performance,
                            'calculation_status' => $eval->calculation_status?->value ?? $eval->calculation_status,
                            'average_score' => $eval->average_score !== null ? (float) $eval->average_score : null,
                        ];

                        if ($sem === SemesterStatus::FullYear->value && $eval->academic_performance) {
                            $fullYearCount++;
                            $apVal = $eval->academic_performance instanceof \BackedEnum ? $eval->academic_performance->value : (string) $eval->academic_performance;
                            $ratingDist[$apVal] = ($ratingDist[$apVal] ?? 0) + 1;
                            $latestRating = [
                                'class' => $className,
                                'value' => $apVal,
                                'label' => RatingLadder::getLabel($apVal),
                            ];
                        }

                        // Tích lũy điểm môn học phục vụ highlights
                        foreach ($eval->subjectGrades as $sg) {
                            if ($sg->grade !== null) {
                                $sid = $sg->subject_id;
                                $sName = $sg->subject?->name ?? "Môn #{$sid}";
                                if (!isset($subjectScoresAccumulator[$sid])) {
                                    $subjectScoresAccumulator[$sid] = ['name' => $sName, 'scores' => []];
                                }
                                $subjectScoresAccumulator[$sid]['scores'][] = (float) $sg->grade;
                            }
                        }
                    }

                    if ($cg->full_year_grade !== null) {
                        $averageTrend[] = [
                            'class' => $className,
                            'value' => (float) $cg->full_year_grade,
                        ];
                    }
                }

                $classDataList[] = [
                    'class_id' => $cid,
                    'name' => $className,
                    'semesters' => $semestersData,
                    'attachments_count' => $totalAttachments,
                ];
            }

            $stages[] = [
                'education_level' => $level,
                'label' => $def['label'],
                'classes_total' => count($def['class_ids']),
                'classes_with_full_year' => $fullYearCount,
                'latest_rating' => $latestRating,
                'rating_distribution' => $ratingDist,
                'average_trend' => $averageTrend,
                'classes' => $classDataList,
            ];
        }

        // Tính highlights
        $subjectAverages = [];
        foreach ($subjectScoresAccumulator as $sid => $data) {
            if (!empty($data['scores'])) {
                $avg = round(array_sum($data['scores']) / count($data['scores']), 1);
                $subjectAverages[] = [
                    'subject_id' => $sid,
                    'name' => $data['name'],
                    'value' => $avg,
                ];
            }
        }

        usort($subjectAverages, fn($a, $b) => $b['value'] <=> $a['value']);
        $strongSubjects = array_values(array_filter($subjectAverages, fn($s) => $s['value'] >= 8.0));
        $needAttention = array_values(array_filter($subjectAverages, fn($s) => $s['value'] < 6.5));

        return [
            'child_id' => $childId,
            'stages' => $stages,
            'highlights' => [
                'strong_subjects' => array_slice($strongSubjects, 0, 3),
                'need_attention' => array_slice($needAttention, 0, 3),
            ],
        ];
    }
}
