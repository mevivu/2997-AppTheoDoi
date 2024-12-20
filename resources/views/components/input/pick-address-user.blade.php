<div class="d-flex justify-content-between align-items-center">
    <label class="form-label">
        <span class="ti ti-map"></span>
        {{ $label }}</label>
    <div id="getCurrentLocation" class="text-danger d-flex align-items-center">
        <div class="spinner-border text-danger me-1" role="status" style="display: none;">
            <span class="visually-hidden">Loading...</span>
        </div>
        <span class="cursor-pointer">@lang('useCurrentLocation')</span>
    </div>
</div>
<div class="input-group mb-2">
    <input type="text" {{ $attributes->class(['form-control'])->merge($isRequired()) }} name="{{ $name }}"
        readonly data-parsley-errors-container="#error{{ $name }}" />
    <button type="button" id="openModalPickAddressUser" class="btn text-danger fw-normal"
        data-input="input[name={{ $name }}]" data-lat="input[name=user_lat]" data-lng="input[name=user_lng]"
        data-address-detail="input[name=address_detail]" data-bs-toggle="modal"
        data-bs-target="#modalPickAddressUser">@lang('pickAddress')</button>
</div>
<div id="error{{ $name }}"></div>
