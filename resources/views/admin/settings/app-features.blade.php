@extends('admin.layouts.master')

@push('libs-css')
    @include('admin.common.css.style')
@endpush

@push('custom-css')
    <style>
        .af-group-card {
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            box-shadow: 0 1px 3px rgba(15, 23, 42, .05);
            overflow: hidden;
        }

        .af-group-header {
            background: linear-gradient(90deg, #f8fafc, #ffffff);
            border-bottom: 1px solid #f1f5f9;
            padding: .9rem 1.25rem;
        }

        .af-row {
            display: grid;
            grid-template-columns: minmax(220px, 2fr) 80px 80px 80px minmax(160px, 1.2fr) minmax(200px, 2fr);
            gap: 1rem;
            align-items: center;
            padding: .85rem 1.25rem;
            border-bottom: 1px solid #f1f5f9;
            transition: background-color .15s, opacity .15s;
        }

        .af-row:last-child { border-bottom: 0; }
        .af-row:hover { background-color: #f8fafc; }
        .af-row.is-head {
            background: #f8fafc;
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
            color: #64748b;
            padding-top: .6rem;
            padding-bottom: .6rem;
        }

        .af-row.is-off .af-name { opacity: .6; }
        .af-row.is-saving { opacity: .6; pointer-events: none; }

        .af-avatar, .af-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
            overflow: hidden;
        }

        .af-avatar {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            transition: transform .15s ease, box-shadow .15s ease;
        }

        .af-row:hover .af-avatar {
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
        }

        .af-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .af-name { font-weight: 600; color: #0f172a; line-height: 1.3; }
        .af-code { font-size: .75rem; color: #64748b; font-family: ui-monospace, SFMono-Regular, Menlo, monospace; }
        .af-meta { font-size: .72rem; color: #94a3b8; }
        .af-switch .form-check-input { cursor: pointer; width: 2.6em; height: 1.4em; }
        .af-switch .form-check-input:checked {
            background-color: #16a34a;
            border-color: #16a34a;
        }
        .af-switch .form-check-input:focus-visible { box-shadow: 0 0 0 3px rgba(22, 163, 74, .25); }
        .af-switch .form-check-input.is-dev-switch:checked {
            background-color: #8b5cf6;
            border-color: #8b5cf6;
        }
        .af-switch .form-check-input.is-dev-switch:focus-visible {
            box-shadow: 0 0 0 3px rgba(139, 92, 246, .25);
        }
        .af-cell-label { display: none; font-size: .7rem; color: #64748b; font-weight: 600; text-transform: uppercase; }

        .af-toast {
            position: fixed;
            right: 24px;
            bottom: 24px;
            z-index: 2000;
            padding: .65rem 1rem;
            border-radius: 10px;
            color: #fff;
            font-size: .875rem;
            font-weight: 500;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, .25);
            opacity: 0;
            transform: translateY(8px);
            transition: opacity .2s, transform .2s;
            pointer-events: none;
        }
        .af-toast.show { opacity: 1; transform: translateY(0); }
        .af-toast.ok { background: #15803d; }
        .af-toast.err { background: #b91c1c; }

        @media (max-width: 991.98px) {
            .af-row.is-head { display: none; }
            .af-row { grid-template-columns: 1fr 1fr 1fr; gap: .75rem 1rem; }
            .af-row > .af-col-name { grid-column: 1 / -1; }
            .af-row > .af-col-msg { grid-column: 1 / -1; }
            .af-cell-label { display: block; margin-bottom: .25rem; }
        }
    </style>
@endpush

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <x-admin.page-header
                class="mb-4"
                icon="toggle-right"
                :title="__('Quản lý chức năng App')"
                :subtitle="__('Bật/tắt từng chức năng hiển thị trong app theo nền tảng Android / iOS / Dev. Thay đổi có hiệu lực ngay khi app tải lại cấu hình.')"
                :back-route="route('admin.dashboard')"
            />

            <div class="alert alert-info d-flex gap-2 align-items-start mb-4" role="alert">
                <i class="ti ti-info-circle fs-3 mt-1"></i>
                <div>
                    <div class="fw-semibold mb-1">{{ __('Cách hoạt động của chế độ khi tắt') }}</div>
                    <ul class="mb-0 ps-3">
                        <li><b>{{ __('Ẩn hẳn') }}</b>: {{ __('chức năng biến mất khỏi app.') }}</li>
                        <li><b>{{ __('Hiện & báo bảo trì') }}</b>: {{ __('chức năng vẫn hiển thị nhưng khi bấm sẽ hiện thông báo thay vì mở.') }}</li>
                        <li><b><span class="badge bg-purple-lt me-1">DEV</span>{{ __('Môi trường Dev') }}</b>: {{ __('Chỉ ảnh hưởng khi app chạy debug/test (không ảnh hưởng người dùng thật trên Store).') }}</li>
                    </ul>
                </div>
            </div>

                @foreach ($groups as $groupKey => $groupName)
                    @php
                        $items = $features->get($groupKey, collect());
                    @endphp
                    @if ($items->isNotEmpty())
                    <div class="af-group-card card mb-4">
                        <div class="af-group-header d-flex justify-content-between align-items-center">
                            <div class="fw-bold fs-4">{{ $groupName }}</div>
                            <span class="badge bg-secondary-lt">{{ $items->count() }} {{ __('chức năng') }}</span>
                        </div>

                        <div class="af-row is-head">
                            <div>{{ __('Chức năng') }}</div>
                            <div>Android</div>
                            <div>iOS</div>
                            <div>Dev <span class="badge bg-purple-lt ms-1" style="font-size: .65rem; padding: 2px 4px;">TEST</span></div>
                            <div>{{ __('Khi tắt') }}</div>
                            <div>{{ __('Thông báo bảo trì') }}</div>
                        </div>

                        @foreach ($items as $feature)
                            @php
                                $meta = $catalog[$feature->code] ?? [];
                                $color = $meta['color'] ?? '#64748b';
                                $icon = $meta['icon'] ?? 'ti ti-apps';
                                $off = !$feature->enabled_android || !$feature->enabled_ios;
                                $avatarRel = 'assets/images/app-features/' . $feature->code . '.png';
                                $hasAvatar = file_exists(public_path($avatarRel)) || file_exists(base_path('public/' . $avatarRel));
                            @endphp
                            <div class="af-row {{ $off ? 'is-off' : '' }}"
                                 data-id="{{ $feature->id }}"
                                 data-url="{{ route('admin.app-feature.update', $feature->id) }}">
                                <div class="af-col-name d-flex align-items-center gap-3">
                                    @if ($hasAvatar)
                                        <span class="af-avatar" title="{{ $feature->name }}">
                                            <img src="{{ asset($avatarRel) }}" alt="{{ $feature->name }}" loading="lazy">
                                        </span>
                                    @else
                                        <span class="af-icon" style="background: {{ $color }}1a; color: {{ $color }};">
                                            <i class="{{ $icon }}"></i>
                                        </span>
                                    @endif
                                    <div class="min-w-0">
                                        <div class="af-name">{{ $feature->name }}</div>
                                        <div class="af-code">{{ $feature->code }}</div>
                                        <div class="af-meta js-updated">
                                            @if ($feature->updated_by)
                                                {{ __('Cập nhật') }}: {{ $feature->updated_at?->format('d/m/Y H:i') }}
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="af-switch">
                                    <span class="af-cell-label">Android</span>
                                    <label class="form-check form-switch m-0" title="Android (Production)">
                                        <input type="hidden" name="features[{{ $feature->id }}][enabled_android]" value="0">
                                        <input class="form-check-input js-field" type="checkbox"
                                               name="features[{{ $feature->id }}][enabled_android]" value="1"
                                               data-field="enabled_android" @checked($feature->enabled_android)
                                               aria-label="Android - {{ $feature->name }}">
                                    </label>
                                </div>

                                <div class="af-switch">
                                    <span class="af-cell-label">iOS</span>
                                    <label class="form-check form-switch m-0" title="iOS (Production)">
                                        <input type="hidden" name="features[{{ $feature->id }}][enabled_ios]" value="0">
                                        <input class="form-check-input js-field" type="checkbox"
                                               name="features[{{ $feature->id }}][enabled_ios]" value="1"
                                               data-field="enabled_ios" @checked($feature->enabled_ios)
                                               aria-label="iOS - {{ $feature->name }}">
                                    </label>
                                </div>

                                <div class="af-switch">
                                    <span class="af-cell-label">Dev</span>
                                    <label class="form-check form-switch m-0" title="{{ __('Môi trường Dev / Lập trình viên') }}">
                                        <input type="hidden" name="features[{{ $feature->id }}][enabled_dev]" value="0">
                                        <input class="form-check-input js-field is-dev-switch" type="checkbox"
                                               name="features[{{ $feature->id }}][enabled_dev]" value="1"
                                               data-field="enabled_dev" @checked($feature->enabled_dev)
                                               aria-label="Dev - {{ $feature->name }}">
                                    </label>
                                </div>

                                <div>
                                    <span class="af-cell-label">{{ __('Khi tắt') }}</span>
                                    <select class="form-select form-select-sm js-field"
                                            name="features[{{ $feature->id }}][disabled_mode]"
                                            data-field="disabled_mode"
                                            aria-label="{{ __('Chế độ khi tắt') }} - {{ $feature->name }}">
                                        <option value="hide" @selected($feature->disabled_mode === 'hide')>{{ __('Ẩn hẳn') }}</option>
                                        <option value="maintenance" @selected($feature->disabled_mode === 'maintenance')>{{ __('Hiện & báo bảo trì') }}</option>
                                    </select>
                                </div>

                                <div class="af-col-msg">
                                    <span class="af-cell-label">{{ __('Thông báo bảo trì') }}</span>
                                    <input type="text" class="form-control form-control-sm js-field js-text"
                                           name="features[{{ $feature->id }}][disabled_message]"
                                           data-field="disabled_message" maxlength="255"
                                           value="{{ $feature->disabled_message }}"
                                           placeholder="{{ __('Mặc định: Chức năng đang bảo trì') }}"
                                           aria-label="{{ __('Thông báo bảo trì') }} - {{ $feature->name }}">
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @endif
                @endforeach

                @if ($total === 0)
                    <div class="empty">
                        <div class="empty-icon"><i class="ti ti-toggle-right fs-1"></i></div>
                        <p class="empty-title">{{ __('Chưa có chức năng nào') }}</p>
                        <p class="empty-subtitle text-muted">{{ __('Chạy migration để khởi tạo danh mục chức năng.') }}</p>
                    </div>
                @endif
        </div>
    </div>

    <div id="afToast" class="af-toast" role="status" aria-live="polite"></div>
@endsection

@push('custom-js')
    <script>
        $(document).ready(function () {
            const csrf = $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}';
            let toastTimer = null;

            function toast(msg, ok) {
                const $t = $('#afToast');
                $t.text(msg).removeClass('ok err show').addClass(ok ? 'ok' : 'err');
                void $t[0].offsetWidth;
                $t.addClass('show');
                clearTimeout(toastTimer);
                toastTimer = setTimeout(() => $t.removeClass('show'), 2200);
            }

            function save($row, payload, $field, revert) {
                $row.addClass('is-saving');
                $.ajax({
                    url: $row.data('url'),
                    type: 'POST',
                    data: Object.assign({_method: 'PUT', _token: csrf}, payload),
                    headers: {'X-CSRF-TOKEN': csrf, 'Accept': 'application/json'},
                })
                    .done(function (res) {
                        const d = res.data || {};
                        $row.toggleClass('is-off', !d.enabled_android || !d.enabled_ios);
                        if (d.updated_at) {
                            $row.find('.js-updated').text('{{ __('Cập nhật') }}: ' + d.updated_at);
                        }
                        toast('{{ __('Đã lưu thay đổi') }}', true);
                    })
                    .fail(function (xhr) {
                        if (typeof revert === 'function') revert();
                        const msg = (xhr.responseJSON && xhr.responseJSON.message) || '{{ __('Lưu thất bại, vui lòng thử lại') }}';
                        toast(msg, false);
                    })
                    .always(function () {
                        $row.removeClass('is-saving');
                    });
            }

            // Switch + select: lưu ngay khi đổi
            $('.af-row .js-field').not('.js-text').on('change', function () {
                const $el = $(this);
                const $row = $el.closest('.af-row');
                const field = $el.data('field');
                const isCheckbox = $el.is(':checkbox');
                const value = isCheckbox ? ($el.is(':checked') ? 1 : 0) : $el.val();
                const payload = {};
                payload[field] = value;

                save($row, payload, $el, function () {
                    if (isCheckbox) {
                        $el.prop('checked', !$el.is(':checked'));
                    }
                });
            });

            // Ô thông báo: lưu khi rời ô nếu có thay đổi
            $('.af-row .js-text').each(function () {
                $(this).data('orig', $(this).val());
            }).on('blur', function () {
                const $el = $(this);
                const val = $el.val();
                if (val === $el.data('orig')) return;
                const $row = $el.closest('.af-row');
                save($row, {disabled_message: val}, $el, function () {
                    $el.val($el.data('orig'));
                });
                $el.data('orig', val);
            });
        });
    </script>
@endpush
