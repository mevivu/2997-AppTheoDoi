<?php

namespace App\Services\ReportCard;

use App\Models\ChildEvaluation;
use App\Models\ChildEvaluationAttachment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

class ReportCardAttachmentService
{
    /**
     * @param ChildEvaluation $evaluation
     * @param UploadedFile[] $files
     * @param int|null $userId
     * @return ChildEvaluationAttachment[]
     */
    public function upload(ChildEvaluation $evaluation, array $files, ?int $userId = null): array
    {
        $maxAttachments = (int) config('report_card.attachments.max_per_evaluation', 10);
        $currentCount = $evaluation->attachments()->count();

        if ($currentCount + count($files) > $maxAttachments) {
            throw new InvalidArgumentException(
                sprintf('Tổng số ảnh vượt quá giới hạn tối đa (%d ảnh/kỳ). Hiện có: %d, thêm mới: %d', $maxAttachments, $currentCount, count($files))
            );
        }

        $childId = $evaluation->classGrade?->child_id ?? 'unknown';
        $disk = config('report_card.attachments.disk', 'local');
        $created = [];

        $currentMaxSort = (int) $evaluation->attachments()->max('sort_order');

        foreach ($files as $index => $file) {
            $extension = strtolower($file->getClientOriginalExtension());
            if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                throw new InvalidArgumentException("Định dạng file không hợp lệ: {$extension}");
            }

            $uuidName = Str::uuid()->toString() . '.' . $extension;
            $dir = "report-cards/{$childId}";
            $path = Storage::disk($disk)->putFileAs($dir, $file, $uuidName);

            $imgSize = @getimagesize($file->getPathname()) ?: [null, null];

            $attachment = ChildEvaluationAttachment::create([
                'child_evaluation_id' => $evaluation->id,
                'disk' => $disk,
                'file_path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType() ?: 'application/octet-stream',
                'size_bytes' => $file->getSize() ?: 0,
                'width' => $imgSize[0],
                'height' => $imgSize[1],
                'sort_order' => $currentMaxSort + $index + 1,
                'uploaded_by' => $userId,
            ]);

            $created[] = $attachment;
        }

        return $created;
    }

    public function delete(ChildEvaluationAttachment $attachment): bool
    {
        $disk = $attachment->disk;
        $path = $attachment->file_path;

        return DB::transaction(function () use ($attachment, $disk, $path) {
            $deleted = $attachment->delete();

            if ($deleted) {
                DB::afterCommit(function () use ($disk, $path) {
                    if (Storage::disk($disk)->exists($path)) {
                        Storage::disk($disk)->delete($path);
                    }
                });
            }

            return (bool) $deleted;
        });
    }

    public function reorder(ChildEvaluation $evaluation, array $attachmentIds): void
    {
        DB::transaction(function () use ($evaluation, $attachmentIds) {
            foreach ($attachmentIds as $index => $id) {
                ChildEvaluationAttachment::where('id', $id)
                    ->where('child_evaluation_id', $evaluation->id)
                    ->update(['sort_order' => $index]);
            }
        });
    }

    public function getFileResponse(ChildEvaluationAttachment $attachment): Response
    {
        $disk = $attachment->disk;
        $path = $attachment->file_path;

        if (!Storage::disk($disk)->exists($path)) {
            abort(404, 'Không tìm thấy tệp ảnh học bạ.');
        }

        return Storage::disk($disk)->response($path, $attachment->original_name);
    }
}
