<!-- Modal Nạp tiền -->
<div class="modal fade"
     id="depositModal"
     tabindex="-1"
     aria-labelledby="depositModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="depositModalLabel">{{ __('Nạp tiền vào ví') }}</h5>
                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <div class="mb-3">
                    <label for="depositAmount" class="form-label">{{ __('Số tiền nạp') }}</label>
                    <x-input-price
                        type="number"
                        class="form-control"
                        id="depositAmount"
                        name="depositAmount"
                        required/>
                </div>

                <x-button class="btn btn-primary" type="button" id="deposit">
                    <span class="button-text">Xác nhận nạp tiền</span>
                    <span class="spinner-border spinner-border-sm d-none"
                          role="status"
                          aria-hidden="true"></span>
                </x-button>

            </div>
        </div>
    </div>
</div>
