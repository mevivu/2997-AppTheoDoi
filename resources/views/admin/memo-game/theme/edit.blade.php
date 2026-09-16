@php use App\Traits\RouteAdminSystem; @endphp
@extends('admin.layouts.master')

@push('libs-css')
    @include('admin.common.css.style')
    @include('admin.common.css.action')
@endpush

@push('custom-css')
    <style>
        .sortable-theme-item {
            user-select: none;
            transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
            cursor: grab;
        }
        .sortable-theme-item:active {
            cursor: grabbing;
        }
        .sortable-theme-item.dragging {
            opacity: 0.45;
            background: #eef2ff !important;
            border: 2px dashed #4f46e5 !important;
            transform: scale(0.98);
        }
        .sortable-theme-item.drag-over {
            border-top: 3px solid #206bc4 !important;
            background: #f0f6ff !important;
        }
        .bg-primary-subtle-light {
            background-color: #f0f7ff !important;
        }
        .cursor-grab {
            cursor: grab !important;
        }

        /* Custom Scrollbar for sortable list */
        .sortable-theme-list::-webkit-scrollbar {
            width: 5px;
        }
        .sortable-theme-list::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }
        .sortable-theme-list::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .sortable-theme-list::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Premium Save Order Button Styling */
        .btn-save-order-custom {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 11px 18px;
            font-size: 0.85rem;
            font-weight: 700;
            border-radius: 50rem;
            border: 1px solid #e2e8f0;
            background: #f1f5f9;
            color: #94a3b8;
            cursor: not-allowed;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: none;
            letter-spacing: 0.02em;
            text-transform: uppercase;
        }

        .btn-save-order-custom:disabled {
            opacity: 0.85;
            cursor: not-allowed;
        }

        .btn-save-order-custom.active-save {
            background: linear-gradient(135deg, #206bc4 0%, #17549c 100%) !important;
            color: #ffffff !important;
            border-color: transparent !important;
            cursor: pointer !important;
            box-shadow: 0 6px 20px -2px rgba(32, 107, 196, 0.45) !important;
            animation: pulse-border 2s infinite;
        }

        .btn-save-order-custom.active-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -2px rgba(32, 107, 196, 0.6) !important;
            background: linear-gradient(135deg, #1d60af 0%, #134683 100%) !important;
        }

        .btn-save-order-custom.active-save:active {
            transform: translateY(0);
        }

        @keyframes pulse-border {
            0% {
                box-shadow: 0 0 0 0 rgba(32, 107, 196, 0.45);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(32, 107, 196, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(32, 107, 196, 0);
            }
        }
    </style>
@endpush

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <x-admin.page-header
                class="mb-4"
                icon="palette"
                :title="__('Cập nhật Chủ đề Memo Game')"
                :subtitle="$response->name"
                :back-route="route(RouteAdminSystem::MEMO_THEME_INDEX)"
            />

            <x-form :action="route('admin.memo-game.theme.update')" type="put" :validate="true" :has-file="true" enctype="multipart/form-data">
                <input type="hidden" name="id" value="{{ $response->id }}">
                <div class="row g-4 justify-content-center">
                    @include('admin.memo-game.theme.forms.edit-left')
                    @include('admin.memo-game.theme.forms.edit-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection

@push('libs-js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('sortable-themes-list');
            const saveBtn = document.getElementById('btn-save-theme-order');
            const positionInput = document.getElementById('theme_position_input');
            const currentThemeId = '{{ $response->id }}';

            if (!container || !saveBtn) return;

            let draggedItem = null;

            function updateBadgeIndexes() {
                const items = container.querySelectorAll('.sortable-theme-item');
                items.forEach((item, idx) => {
                    const badge = item.querySelector('.theme-badge-pos');
                    if (badge) {
                        badge.textContent = '#' + (idx + 1);
                    }
                    if (item.dataset.id === currentThemeId && positionInput) {
                        positionInput.value = idx + 1;
                    }
                });
            }

            function activateSaveButton() {
                saveBtn.removeAttribute('disabled');
                saveBtn.classList.add('active-save');
                const btnText = saveBtn.querySelector('.btn-text');
                if (btnText) {
                    btnText.textContent = '{{ __("Lưu thứ tự vị trí mới") }}';
                }
            }

            function deactivateSaveButton(savedText) {
                saveBtn.disabled = true;
                saveBtn.classList.remove('active-save');
                const btnText = saveBtn.querySelector('.btn-text');
                if (btnText) {
                    btnText.textContent = savedText || '{{ __("Chưa có thay đổi thứ tự") }}';
                }
            }

            container.addEventListener('dragstart', function (e) {
                const item = e.target.closest('.sortable-theme-item');
                if (!item) return;
                draggedItem = item;
                setTimeout(() => item.classList.add('dragging'), 0);
                e.dataTransfer.effectAllowed = 'move';
            });

            container.addEventListener('dragend', function (e) {
                const item = e.target.closest('.sortable-theme-item');
                if (item) item.classList.remove('dragging');
                draggedItem = null;
                container.querySelectorAll('.sortable-theme-item').forEach(el => el.classList.remove('drag-over'));
                updateBadgeIndexes();
                activateSaveButton();
            });

            container.addEventListener('dragover', function (e) {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';
                const target = e.target.closest('.sortable-theme-item');
                if (!target || target === draggedItem) return;

                const rect = target.getBoundingClientRect();
                const next = (e.clientY - rect.top) / (rect.bottom - rect.top) > 0.5;

                if (next) {
                    container.insertBefore(draggedItem, target.nextSibling);
                } else {
                    container.insertBefore(draggedItem, target);
                }
            });

            saveBtn.addEventListener('click', function () {
                const items = container.querySelectorAll('.sortable-theme-item');
                const positions = Array.from(items).map(item => item.dataset.id);

                if (!positions.length) return;

                saveBtn.disabled = true;
                saveBtn.classList.remove('active-save');
                saveBtn.innerHTML = '<i class="ti ti-loader-2 spin me-1.5 fs-5"></i> <span>{{ __("ĐANG LƯU VỊ TRÍ...") }}</span>';

                fetch('{{ route(RouteAdminSystem::MEMO_THEME_UPDATE_POSITION) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ positions: positions })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        saveBtn.innerHTML = '<i class="ti ti-circle-check me-1.5 fs-5 text-success"></i> <span>{{ __("ĐÃ LƯU THÀNH CÔNG!") }}</span>';
                        if (typeof msgSuccess === 'function') {
                            msgSuccess(data.message || '{{ __("Cập nhật thứ tự các chủ đề thành công!") }}');
                        } else if (typeof $.toast === 'function') {
                            $.toast({
                                heading: '{{ __("Thành công") }}',
                                text: data.message || '{{ __("Cập nhật thứ tự các chủ đề thành công!") }}',
                                position: 'top-right',
                                icon: 'success'
                            });
                        }
                        setTimeout(() => {
                            saveBtn.innerHTML = '<i class="ti ti-device-floppy me-1.5 fs-5"></i> <span class="btn-text">{{ __("Chưa có thay đổi thứ tự") }}</span>';
                            deactivateSaveButton('{{ __("Chưa có thay đổi thứ tự") }}');
                        }, 2500);
                    } else {
                        activateSaveButton();
                        saveBtn.innerHTML = '<i class="ti ti-device-floppy me-1.5 fs-5"></i> <span class="btn-text">{{ __("Lưu thứ tự vị trí mới") }}</span>';
                        if (typeof msgError === 'function') {
                            msgError(data.message || '{{ __("Có lỗi xảy ra.") }}');
                        } else {
                            alert(data.message || 'Có lỗi xảy ra.');
                        }
                    }
                })
                .catch(err => {
                    activateSaveButton();
                    saveBtn.innerHTML = '<i class="ti ti-device-floppy me-1.5 fs-5"></i> <span class="btn-text">{{ __("Lưu thứ tự vị trí mới") }}</span>';
                    if (typeof msgError === 'function') {
                        msgError('{{ __("Không thể lưu thứ tự chủ đề. Vui lòng kiểm tra lại kết nối.") }}');
                    } else {
                        alert('Không thể lưu thứ tự chủ đề. Vui lòng kiểm tra lại kết nối.');
                    }
                });
            });
        });
    </script>
@endpush
