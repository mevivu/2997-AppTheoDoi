@php use App\Enums\Notification\MessageType;use App\Models\User; @endphp

<div class="col-12 col-md-9">
    <div class="card custom-shadow">
        <div class="row card-body">
            <div class="col-12">
                <div class="mb-3">
                    @if ($notification->user_id != null)
                        <label class="control-label">
                            <i class="ti ti-user"></i>
                            @lang('Nhân viên nhận')</label>
                        <x-input :value="$notification->user->fullname" name="user_id" :required="true"
                                 :placeholder="__('Nhân viên nhận')" readonly/>
                    @else
                        <label class="control-label">
                            <i class="ti ti-user"></i>
                            @lang('Admin nhận')</label>
                        <x-input :value="$notification->admin->fullname" name="admin_id" :required="true"
                                 :placeholder="__('Admin nhận')" readonly/>
                    @endif
                </div>
            </div>
            <!-- title -->
            <div class="col-12">
                <div class="mb-3">
                    <i class="ti ti-bell-ringing"></i>
                    <label class="control-label">@lang('title')</label>
                    <x-input :value="$notification->title"
                             name="title"
                             :required="true"
                             :placeholder="__('title')"/>
                </div>
            </div>
            <!-- message -->
            <div class="col-12">
                <div class="mb-3">
                    <i class="ti ti-chart-bubble"></i>
                    <label class="control-label">@lang('message')</label>
                    <textarea class="form-control"
                              name="message"
                              required placeholder="{{ __('message') }}">{{ $notification->message }}</textarea>
                </div>
            </div>

            @if($notification->type == MessageType::PAYMENT)
                @php
                    $user = User::find($notification->user_id_attribute);
                @endphp

                @if($user)
                    <div class="col-12">
                        <div class="mb-3">
                            <i class="ti ti-user"></i>
                            <label class="control-label">@lang('fullname'):</label>
                            <x-link :href="route('admin.user.edit', $user->id)" class="w" :title="$user->fullname"/>
                        </div>
                    </div>
                @else
                    <div class="col-12">
                        <div class="mb-3">
                            <i class="ti ti-user"></i>
                            <label class="control-label">@lang('User not found')</label>
                        </div>
                    </div>
                @endif
            @endif



            @if($notification->type  == MessageType::PAYMENT)
                <!-- package -->
                <div class="col-12">
                    <div class="mb-3">
                        <i class="ti ti-bell-ringing"></i>
                        <label class="control-label">@lang('package')</label>
                        <x-input :value="$notification->package->name"
                                 disabled
                                 :required="true"
                                 :placeholder="__('title')"/>
                    </div>
                </div>
            @endif


        </div>
    </div>
</div>
