@extends('admin.layouts.guest.master')
@section('title', 'Đang ngoại tuyến')
@section('content')
<div class="page page-center" style="min-height: 80vh; display: flex; align-items: center; justify-content: center;">
    <div class="container container-narrow py-4 text-center">
        <div class="empty">
            <div class="empty-icon mb-3">
                <i class="ti ti-wifi-off text-danger" style="font-size: 3.5rem;"></i>
            </div>
            <h2 class="empty-title fw-bold">Không có kết nối mạng</h2>
            <p class="empty-subtitle text-muted max-w-md mx-auto my-3">
                Để bảo mật dữ liệu quản trị, hệ thống CMS không lưu dữ liệu làm việc ngoại tuyến. Vui lòng kiểm tra lại kết nối mạng của bạn và thử lại.
            </p>
            <div class="empty-action mt-4">
                <button type="button" class="btn btn-primary btn-pill px-4 py-2" onclick="location.reload()">
                    <i class="ti ti-refresh me-2"></i> Thử lại
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
