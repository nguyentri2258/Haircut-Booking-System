<div
    class="modal fade"
    id="emailVerificationModal"
    tabindex="-1"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white position-relative justify-content-center">
                <h5 class="modal-title">Xác nhận email</h5>
                <button
                    type="button"
                    class="btn-close btn-close-white position-absolute end-0 me-3"
                    id="closeOtpBtn"
                    aria-label="Close"
                ></button>
            </div>
            <div class="modal-body text-center p-4">
                <p class="mb-3">
                    Nhập mã xác nhận <strong>6 chữ số</strong> đã gửi tới email của bạn
                </p>
                <input
                    type="text"
                    id="otp-code"
                    class="form-control text-center fs-4"
                    maxlength="6"
                    inputmode="numeric"
                    autocomplete="one-time-code"
                >
                <div class="mt-3">
                    <span id="otpCountdownText">
                        Gửi lại mã sau
                        <strong><span id="otpCountdown">60</span>s</strong>
                    </span>
                    <button
                        id="otpResendBtn"
                        type="button"
                        class="btn btn-link d-none"
                    >
                        Gửi lại mã
                    </button>
                </div>
                <div
                    id="otpError"
                    class="text-danger mt-2 d-none"
                >
                    Mã xác nhận không đúng hoặc đã hết hạn
                </div>
            </div>
            <div class="modal-footer">
                <button
                    id="verify-otp-btn"
                    type="button"
                    class="btn btn-primary"
                >
                    Xác nhận
                </button>
            </div>           
        </div>
    </div>
</div>