@php use App\Enums\Notification\MessageType; @endphp

<div class="col-12 col-md-9">
    <div class="card">
        <div class="row card-body">
            <div class="col-12">
                <div class="mb-3">
                    @if ($notification->user_id != null)
                        <label class="control-label">
                            <i class="ti ti-user"></i>
                            @lang('Nhân viên nhận')</label>
                        <x-input :value="$notification->user->fullname" name="user_id" :required="true" :placeholder="__('Nhân viên nhận')" readonly />
                    @else
                        <label class="control-label">
                            <i class="ti ti-user"></i>
                            @lang('Admin nhận')</label>
                        <x-input :value="$notification->admin->fullname" name="admin_id" :required="true" :placeholder="__('Admin nhận')" readonly />
                    @endif
                </div>
            </div>
            <!-- title -->
            <div class="col-12">
                <div class="mb-3">
                    <i class="ti ti-bell-ringing"></i>
                    <label class="control-label">@lang('title')</label>
                    <x-input :value="$notification->title" name="title" :required="true" :placeholder="__('title')" />
                </div>
            </div>
            <!-- message -->
            <div class="col-12">
                <div class="mb-3">
                    <i class="ti ti-chart-bubble"></i>
                    <label class="control-label">@lang('message')</label>
                    <x-input :value="$notification->message" name="message" :required="true" :placeholder="__('message')" />
                </div>
            </div>
        </div>
    </div>
</div>
