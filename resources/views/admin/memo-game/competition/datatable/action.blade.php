@php use App\Traits\RouteAdminSystem; @endphp
<x-admin.datatable.action-group>
    <a href="{{ route(RouteAdminSystem::MEMO_COMPETITION_LEADERBOARD, $id) }}" 
       class="dt-action-btn dt-action-view" 
       title="{{ __('Xem Bảng Xếp Hạng & Tra Cứu') }}"
       style="background: #fef3c7; color: #d97706; border-color: #fde68a;">
        <i class="ti ti-trophy"></i>
    </a>
    <x-admin.datatable.action-edit :href="route(RouteAdminSystem::MEMO_COMPETITION_EDIT, $id)" />
    <x-button.modal-delete 
        class="dt-action-btn dt-action-delete"
        data-route="{{ route(RouteAdminSystem::MEMO_COMPETITION_DELETE, $id) }}"
        data-modal-title="{{ __('Xác nhận xóa giải đấu?') }}"
        data-modal-desc="Khi xóa giải đấu <strong>&quot;{{ e($name) }}&quot;</strong>, các dữ liệu sau sẽ bị xóa vĩnh viễn khỏi hệ thống:<ul class='text-start mt-2 mb-0 ps-3 fs-12 text-muted' style='line-height: 1.6;'><li>Toàn bộ cấu hình 4 ván thi và chủ đề của giải đấu</li><li>Toàn bộ lượt thi của các bé đã tham gia</li><li>Toàn bộ kết quả chi tiết từng ván chơi &amp; bảng xếp hạng</li><li>File ảnh banner đại diện giải đấu trên server</li></ul><div class='alert alert-warning py-1 px-2 fs-11 mt-2 mb-0 text-start'><i class='ti ti-alert-triangle me-1 text-warning'></i><strong>Lưu ý:</strong> Hành động này không thể hoàn tác!</div>"
        title="{{ __('Xóa') }}"
    />
</x-admin.datatable.action-group>
