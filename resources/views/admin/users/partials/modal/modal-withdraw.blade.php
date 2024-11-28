<!-- Modal Rút tiền -->
<div class="modal fade"
     id="withdrawModal"
     tabindex="-1"
     aria-labelledby="withdrawModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"
                    id="withdrawModalLabel">{{ __('Rút tiền từ ví') }}
                </h5>
                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <!-- Số tiền rút -->
                <div class="mb-3">
                    <label for="withdrawAmount" class="form-label">{{ __('Số tiền rút') }}</label>
                    <x-input-price
                        type="number"
                        class="form-control"
                        id="withdrawAmount"
                        name="withdrawAmount"
                        required/>
                </div>

                <x-button class="btn btn-primary" type="button" id="withdraw">
                    <span class="button-text">Xác nhận rút tiền</span>
                    <span class="spinner-border spinner-border-sm d-none"
                          role="status" aria-hidden="true">

                    </span>
                </x-button>

            </div>
        </div>
    </div>
</div>
