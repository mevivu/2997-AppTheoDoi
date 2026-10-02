<div class="col-12 col-md-9">
    <div class="card custom-shadow">
        <div class="card-header justify-content-between">
            <h2 class="mb-0">{{ __('Thông tin Chuyên gia / Cố vấn') }}</h2>
        </div>
        <div class="row card-body">
            <!-- council_type -->
            <div class="col-12 col-md-6">
                <div class="mb-3">
                    <label class="control-label">
                        <span class="ti ti-users-group"></span>
                        {{ __('Nhóm Hội đồng Cố vấn') }}: <span class="text-danger">*</span>
                    </label>
                    <x-select name="council_type" :required="true">
                        @foreach (\App\Enums\Expert\ExpertCouncilType::asSelectArray() as $key => $value)
                            <x-select-option :value="$key" :title="$value" :selected="old('council_type', 2) == $key" />
                        @endforeach
                    </x-select>
                </div>
            </div>

            <!-- name -->
            <div class="col-12 col-md-6">
                <div class="mb-3">
                    <label class="control-label">
                        <span class="ti ti-id"></span>
                        {{ __('Họ và tên') }}: <span class="text-danger">*</span>
                    </label>
                    <x-input name="name" :value="old('name')" :required="true" placeholder="{{ __('VD: Ths. Ngô Văn Tụ, Ts. Hoàng Trung Dũng...') }}" />
                </div>
            </div>

            <!-- title -->
            <div class="col-12 col-md-6">
                <div class="mb-3">
                    <label class="control-label">
                        <span class="ti ti-certificate"></span>
                        {{ __('Chức danh / Học vị / Vị trí') }}: <span class="text-danger">*</span>
                    </label>
                    <x-input name="title" :value="old('title')" :required="true" placeholder="{{ __('VD: Nguyên TGĐ Vinamilk, Bác sĩ CKII, Giảng viên...') }}" />
                </div>
            </div>

            <!-- workplace -->
            <div class="col-12 col-md-6">
                <div class="mb-3">
                    <label class="control-label">
                        <span class="ti ti-building"></span>
                        {{ __('Cơ quan / Doanh nghiệp / Tổ chức') }}:
                    </label>
                    <x-input name="workplace" :value="old('workplace')" placeholder="{{ __('VD: Vinamilk, Học viện Kingsman, ĐH Sư phạm...') }}" />
                </div>
            </div>

            <!-- hospital -->
            <div class="col-12 col-md-6">
                <div class="mb-3">
                    <label class="control-label">
                        <span class="ti ti-building-hospital"></span>
                        {{ __('Bệnh viện / Viện nghiên cứu công tác (nếu có)') }}:
                    </label>
                    <x-input name="hospital" :value="old('hospital')" placeholder="{{ __('VD: Bệnh viện Nhi Đồng 1, Viện Dinh Dưỡng...') }}" />
                </div>
            </div>

            <!-- contact_phone -->
            <div class="col-12 col-md-6">
                <div class="mb-3">
                    <label class="control-label">
                        <span class="ti ti-phone"></span>
                        {{ __('Số điện thoại tư vấn') }}:
                    </label>
                    <x-input name="contact_phone" :value="old('contact_phone')" placeholder="{{ __('VD: 0912345678') }}" />
                </div>
            </div>

            <!-- contact_link -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">
                        <span class="ti ti-link"></span>
                        {{ __('Đường dẫn liên kết ("Nút Liên hệ" trên App)') }}:
                    </label>
                    <x-input name="contact_link" :value="old('contact_link')" placeholder="{{ __('VD: https://zalo.me/... hoặc https://facebook.com/...') }}" />
                </div>
            </div>

            <!-- bio -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">
                        <span class="ti ti-file-text"></span>
                        {{ __('Tiểu sử / Quá trình đào tạo & Thành tựu') }}:
                    </label>
                    <textarea name="bio" class="ckeditor visually-hidden">{{ old('bio') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    {{-- Danh sách Học vị & Bằng cấp chuyên môn --}}
    @include('admin.experts.forms.qualifications')
</div>

@push('custom-js')
    @php
        $initialQualifications = old('qualifications', []);
    @endphp
    @include('admin.experts.scripts.qualification-script', ['initialQualifications' => $initialQualifications])
@endpush

