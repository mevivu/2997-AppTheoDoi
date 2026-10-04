<?php

namespace App\Services\ChildScore;

interface ChildScoreAggregatorInterface
{
    /**
     * Lấy điểm mới nhất của cả 5 chỉ số (IQ, EQ, AQ, GPA, PQ) cho một trẻ em
     *
     * @param int $childId
     * @return array [ 'iq' => ?float, 'eq' => ?float, 'aq' => ?float, 'gpa' => ?float, 'pq' => ?float ]
     */
    public function getLatestScores(int $childId): array;

    /**
     * Chuẩn hóa điểm thô về thang điểm 0 - 100
     *
     * @param string $type 'iq' | 'eq' | 'aq' | 'gpa' | 'pq'
     * @param float|null $raw
     * @return float|null
     */
    public function normalize(string $type, ?float $raw): ?float;

    /**
     * Tính điểm trung bình phát triển tổng hợp của các con thuộc về một phụ huynh (thang 0 - 100)
     * Chỉ tính các chỉ số đã có điểm thực tế (khác null). Nếu chưa có con hoặc chưa có chỉ số nào thì trả về 0.
     *
     * @param int $userId
     * @return float
     */
    public function getAverageNormalizedScoreForUser(int $userId): float;
}
