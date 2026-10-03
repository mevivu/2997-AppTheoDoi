<?php

namespace App\Api\V1\Http\Controllers\ChildEvaluation;

use App\Admin\Http\Controllers\Controller;
use App\Api\V1\Exception\ReportCardAccessDeniedException;
use App\Api\V1\Http\Requests\ChildEvaluation\ChildEvaluationInfoRequest;
use App\Api\V1\Http\Requests\ChildEvaluation\ChildEvaluationRequest;
use App\Api\V1\Http\Resources\ChildEvaluation\ChildEvaluationAttachmentResource;
use App\Api\V1\Http\Resources\ChildEvaluation\ChildEvaluationInfoResource;
use App\Api\V1\Http\Resources\ChildEvaluation\ChildEvaluationResourceCollection;
use App\Api\V1\Repositories\ChildEvaluation\ChildEvaluationRepositoryInterface;
use App\Api\V1\Repositories\ClassGrade\ClassGradeRepositoryInterface;
use App\Api\V1\Services\ChildEvaluation\ChildEvaluationServiceInterface;
use App\Api\V1\Services\ChildEvaluation\ReportCardAccessGuard;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use App\Models\ChildEvaluation;
use App\Models\ChildEvaluationAttachment;
use App\Services\ReportCard\ReportCardAttachmentService;
use App\Services\ReportCard\ReportCardSummaryService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * @group Đánh giá năng lực
 */
class ChildEvaluationController extends Controller
{
    use AuthServiceApi, Response, UseLog;

    protected ClassGradeRepositoryInterface $classGradeRepository;
    protected ReportCardAccessGuard $accessGuard;
    protected ReportCardAttachmentService $attachmentService;
    protected ReportCardSummaryService $summaryService;

    public function __construct(
        ChildEvaluationRepositoryInterface $repository,
        ClassGradeRepositoryInterface     $classGradeRepository,
        ChildEvaluationServiceInterface    $service,
        ReportCardAccessGuard              $accessGuard,
        ?ReportCardAttachmentService       $attachmentService = null,
        ?ReportCardSummaryService          $summaryService = null
    )
    {
        $this->repository = $repository;
        $this->classGradeRepository = $classGradeRepository;
        $this->service = $service;
        $this->accessGuard = $accessGuard;
        $this->attachmentService = $attachmentService ?? app(ReportCardAttachmentService::class);
        $this->summaryService = $summaryService ?? app(ReportCardSummaryService::class);
        $this->middleware('auth:api')->except(['getAttachmentFile']);
    }

    /**
     * Lấy danh sách đánh giá năng lực theo ID của bảng điểm lớp.
     *
     * @authenticated
     * @queryParam class_id int required ID của lớp học. Example: 1
     * @queryParam semester string required Kỳ học. Example: semester_1
     * @queryParam child_id int required ID của trẻ cần lấy đánh giá. Example: 1
     *
     * @response 200 {
     *     "status": "success",
     *     "message": "Dữ liệu đánh giá năng lực được truy xuất thành công.",
     *     "data": [Danh sách đánh giá năng lực]
     * }
     * @response 404 {
     *     "status": "error",
     *     "message": "Không tìm thấy bản ghi."
     * }
     * @response 500 {
     *     "status": "error",
     *     "message": "Lỗi server nội bộ."
     * }
     *
     * @param ChildEvaluationInfoRequest $request
     * @return JsonResponse
     */
    public function findByClassGrade(ChildEvaluationInfoRequest $request): JsonResponse
    {
        try {
            $this->accessGuard->assertOwnsChild($request->validated()['child_id'] ?? null);
            $response = $this->service->findByClass($request);
            return $this->jsonResponseSuccess(new ChildEvaluationInfoResource($response));
        } catch (ReportCardAccessDeniedException $e) {
            return $this->jsonResponseError($e->getMessage(), 403);
        } catch (Exception $e) {
            $this->logError('findByClassGrade:', $e);
            return $this->jsonResponseError('Lỗi server nội bộ khi truy xuất đánh giá năng lực.', 500);
        }
    }

    /**
     * Cập nhật Đánh giá năng lực cho một trẻ cụ thể.
     *
     * Phương thức này nhận dữ liệu từ body của request để cập nhật thông tin đánh giá năng lực.
     * Dữ liệu có thể bao gồm học lực, hạnh kiểm, điểm số của các môn học, phẩm chất, và năng lực.
     *
     * @authenticated
     * @bodyParam child_evaluation_id int required ID của Đánh giá năng lực cần cập nhật.
     * @bodyParam class_grade_id int required ID của lớp. Example: 1
     * @bodyParam semester string required Kỳ học. Example: semester_1
     * @bodyParam status string required Trạng thái của đánh giá. Example: draft
     * @bodyParam conduct string required Hạnh kiểm của học sinh. Example: good
     * @bodyParam academic_performance string required Học lực của học sinh. Example: excellent
     * @bodyParam subjects array required Mảng các môn học và điểm số. Example: [{'id': 1, 'grade': 9.5}]
     * @bodyParam qualities array required Mảng các phẩm chất. Example: [{'id': 1, 'quality_status': 'achieved'}]
     * @bodyParam capabilities array required Mảng các năng lực. Example: [{'id': 1, 'capability_status': 'developed'}]
     *
     * @response 200 {
     *     "status": 200,
     *     "message": "Đánh giá năng lực được cập nhật thành công.",
     *     "data": {
     *         "id": 12,
     *         "class_grade_id": 1,
     *         "semester": "semester_1",
     *         "status": "draft",
     *         "conduct": "good",
     *         "academic_performance": "excellent",
     *         "average_score": 8.333333333333334,
     *         "updated_at": "2024-12-26T07:38:17.000000Z"
     *     }
     * }
     *
     * @response 400 {
     *     "status": 400,
     *     "message": "Dữ liệu nhập vào không hợp lệ."
     * }
     *
     * @response 404 {
     *     "status": 404,
     *     "message": "Đánh giá năng lực không tồn tại."
     * }
     *
     * @response 500 {
     *     "status": 500,
     *     "message": "Lỗi hệ thống."
     * }
     *
     * @param ChildEvaluationRequest $request
     * @return JsonResponse
     */
    public function update(ChildEvaluationRequest $request): JsonResponse
    {
        try {
            $this->accessGuard->assertOwnsEvaluation($request->validated()['child_evaluation_id'] ?? null);
        } catch (ReportCardAccessDeniedException $e) {
            return $this->jsonResponseError($e->getMessage(), 403);
        }

        DB::beginTransaction();
        try {
            $response = $this->service->update($request);
            DB::commit();
            return $this->jsonResponseSuccess($response);
        } catch (Exception $e) {
            DB::rollBack();
            $this->logError('Update child evaluation failed:', $e);
            return $this->jsonResponseError('Lỗi hệ thống khi cập nhật đánh giá năng lực', 500);
        }
    }


    /**
     * Lấy danh sách đánh giá năng lực theo ID của trẻ.
     *
     * Phương thức này truy xuất danh sách phân trang các đánh giá năng lực của một trẻ cụ thể,
     * cho phép lọc và phân trang để quản lý khối lượng dữ liệu.
     *
     * @authenticated
     * @queryParam child_id int required ID của trẻ cần lấy đánh giá. Example: 1
     * @queryParam page int optional Trang hiện tại của kết quả phân trang. Example: 2
     * @queryParam limit int optional Số lượng kết quả mỗi trang. Example: 20
     *
     * @response 200 {
     *     "status": 200,
     *     "message": "Lấy danh sách đánh giá năng lực của trẻ thành công.",
     *     "data": {
     *         "current_page": 1,
     *         "data": [
     *             {
     *                 "id": 12,
     *                 "class_grade": {
     *                     "id": 1,
     *                     "name": "Lớp 1"
     *                 },
     *                 "semester": "Học kỳ 1",
     *                 "average_score": 8.3,
     *                 "academic_performance": "Giỏi",
     *                 "conduct": "Xuất sắc",
     *                 "created_at": "2024-12-26T07:38:17.000000Z",
     *                 "updated_at": "2024-12-26T07:38:17.000000Z"
     *             }
     *         ],
     *         "first_page_url": "http://example.com/api/child-evaluations?page=1",
     *         "from": 1,
     *         "last_page": 1,
     *         "last_page_url": "http://example.com/api/child-evaluations?page=1",
     *         "next_page_url": null,
     *         "path": "http://example.com/api/child-evaluations",
     *         "per_page": 10,
     *         "prev_page_url": null,
     *         "to": 1,
     *         "total": 1
     *     }
     * }
     *
     * @response 400 {
     *     "status": 400,
     *     "message": "Dữ liệu nhập vào không hợp lệ."
     * }
     *
     * @response 500 {
     *     "status": 500,
     *     "message": "Lỗi hệ thống khi lấy danh sách đánh giá năng lực của trẻ."
     * }
     *
     * @param ChildEvaluationRequest $request
     * @return JsonResponse
     */
    public function index(ChildEvaluationRequest $request): JsonResponse
    {
        try {
            $this->accessGuard->assertOwnsChild($request->validated()['child_id'] ?? null);
            $response = $this->service->index($request);
            return $this->jsonResponseSuccess(new ChildEvaluationResourceCollection($response));
        } catch (ReportCardAccessDeniedException $e) {
            return $this->jsonResponseError($e->getMessage(), 403);
        } catch (Exception $exception) {
            $this->logError('Get Children List failed:', $exception);
            return $this->jsonResponseError('Lỗi hệ thống khi lấy danh sách đứa trẻ.', 500);
        }
    }

    /**
     * Bảng tổng hợp tiến trình học bạ của trẻ qua các cấp học
     */
    public function summary(Request $request): JsonResponse
    {
        $childId = $request->query('child_id');
        if (!$childId) {
            return $this->jsonResponseError('child_id là bắt buộc.', 422);
        }

        try {
            $this->accessGuard->assertOwnsChild($childId);
            $summary = $this->summaryService->getSummary((int) $childId);
            return $this->jsonResponseSuccess($summary);
        } catch (ReportCardAccessDeniedException $e) {
            return $this->jsonResponseError($e->getMessage(), 403);
        } catch (Exception $e) {
            $this->logError('summary failed:', $e);
            return $this->jsonResponseError('Lỗi server khi lấy dữ liệu tổng hợp học bạ.', 500);
        }
    }

    /**
     * Tải lên ảnh học bạ đính kèm
     */
    public function uploadAttachments(Request $request, int $id): JsonResponse
    {
        try {
            $this->accessGuard->assertOwnsEvaluation($id);
        } catch (ReportCardAccessDeniedException $e) {
            return $this->jsonResponseError($e->getMessage(), 403);
        }

        $files = $request->file('images') ?? $request->file('files');
        if (!$files && $request->hasFile('image')) {
            $files = [$request->file('image')];
        } elseif (!$files && $request->hasFile('file')) {
            $files = [$request->file('file')];
        }

        if (empty($files) || !is_array($files)) {
            return $this->jsonResponseError('Vui lòng chọn ít nhất một hình ảnh để tải lên.', 422);
        }

        foreach ($files as $file) {
            if (!$file->isValid()) {
                return $this->jsonResponseError('Tệp tải lên không hợp lệ hoặc bị lỗi truyền tải.', 422);
            }
            $ext = strtolower($file->getClientOriginalExtension());
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                return $this->jsonResponseError("Định dạng file .{$ext} không được hỗ trợ. Chỉ chấp nhận jpg, jpeg, png, webp.", 422);
            }
            if ($file->getSize() > 5120 * 1024) {
                return $this->jsonResponseError('Dung lượng mỗi ảnh không được vượt quá 5MB.', 422);
            }
        }

        $evaluation = ChildEvaluation::findOrFail($id);

        try {
            $created = $this->attachmentService->upload($evaluation, $files, auth()->id());
            return $this->jsonResponseSuccess(ChildEvaluationAttachmentResource::collection($created), 'Tải ảnh học bạ lên thành công.');
        } catch (\InvalidArgumentException $e) {
            return $this->jsonResponseError($e->getMessage(), 422);
        } catch (Exception $e) {
            $this->logError('Upload attachments failed:', $e);
            return $this->jsonResponseError('Lỗi server khi tải ảnh học bạ lên.', 500);
        }
    }

    /**
     * Xóa ảnh học bạ đính kèm
     */
    public function deleteAttachment(int $id, int $attachmentId): JsonResponse
    {
        try {
            $this->accessGuard->assertOwnsEvaluation($id);
            $this->accessGuard->assertOwnsAttachment($attachmentId);
        } catch (ReportCardAccessDeniedException $e) {
            return $this->jsonResponseError($e->getMessage(), 403);
        }

        $attachment = ChildEvaluationAttachment::where('id', $attachmentId)
            ->where('child_evaluation_id', $id)
            ->first();

        if (!$attachment) {
            return $this->jsonResponseError('Không tìm thấy tệp đính kèm tương ứng.', 404);
        }

        try {
            $this->attachmentService->delete($attachment);
            return $this->jsonResponseSuccess(null, 'Xóa ảnh học bạ thành công.');
        } catch (Exception $e) {
            $this->logError('Delete attachment failed:', $e);
            return $this->jsonResponseError('Lỗi khi xóa ảnh học bạ.', 500);
        }
    }

    /**
     * Sắp xếp lại thứ tự ảnh học bạ
     */
    public function reorderAttachments(Request $request, int $id): JsonResponse
    {
        try {
            $this->accessGuard->assertOwnsEvaluation($id);
        } catch (ReportCardAccessDeniedException $e) {
            return $this->jsonResponseError($e->getMessage(), 403);
        }

        $ids = $request->input('attachment_ids') ?? $request->input('ids') ?? $request->input('order');
        if (empty($ids) || !is_array($ids)) {
            return $this->jsonResponseError('Danh sách id thứ tự ảnh là bắt buộc.', 422);
        }

        // Nếu gửi dạng [['id' => 1, 'sort_order' => 1], ...]
        if (isset($ids[0]) && is_array($ids[0]) && isset($ids[0]['id'])) {
            usort($ids, fn($a, $b) => ($a['sort_order'] ?? 0) <=> ($b['sort_order'] ?? 0));
            $ids = array_column($ids, 'id');
        }

        $evaluation = ChildEvaluation::findOrFail($id);
        $this->attachmentService->reorder($evaluation, $ids);

        $fresh = $evaluation->attachments()->orderBy('sort_order')->get();
        return $this->jsonResponseSuccess(ChildEvaluationAttachmentResource::collection($fresh), 'Cập nhật thứ tự ảnh thành công.');
    }

    /**
     * Tải/Xem ảnh học bạ (yêu cầu chữ ký temporarySignedRoute)
     */
    public function getAttachmentFile(int $attachmentId): SymfonyResponse
    {
        $attachment = ChildEvaluationAttachment::findOrFail($attachmentId);
        return $this->attachmentService->getFileResponse($attachment);
    }
}
