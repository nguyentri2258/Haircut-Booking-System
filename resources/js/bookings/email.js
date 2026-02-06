document.addEventListener('DOMContentLoaded', () => {
    const $ = id => document.getElementById(id);

    const els = {
        form: $('bookingForm'),
        email: $('email'),
        modal: $('emailVerificationModal'),
        submitBtn: $('submit-btn'),
        otp: $('otp-code'),
        verifyOtpBtn: $('verify-otp-btn'),
        resendBtn: $('otpResendBtn'),
        countdown: $('otpCountdown'),
        countdownText: $('otpCountdownText'),
        otpError: $('otpError'),
        closeBtn: $('closeOtpBtn')
    };

    if (!els.form || !els.modal || !els.submitBtn) return;

    const modal = new bootstrap.Modal(els.modal);

    let verified = false;
    let submitting = false;
    let countdownTimer = null;

    const required = {
        name: 'Vui lòng điền tên',
        phone: 'Vui lòng điền số điện thoại',
        email: 'Vui lòng điền email',
        address_id: 'Vui lòng chọn chi nhánh',
        date: 'Vui lòng chọn ngày',
        user_id: 'Vui lòng chọn stylist',
        time_of_day: 'Vui lòng chọn giờ'
    };

    function post(url, data = {}) {
        return fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        }).then(async r => {
            const json = await r.json().catch(() => ({}));
            if (!r.ok) {
                const err = new Error(json.message || 'Request failed');
                err.status = r.status;
                err.data = json;
                throw err;
            }
            return json;
        });
    }

    function showOtpError(msg = '') {
        if (!els.otpError) return;
        if (!msg) {
            els.otpError.classList.add('d-none');
            els.otpError.textContent = '';
            return;
        }
        els.otpError.textContent = msg;
        els.otpError.classList.remove('d-none');
    }

    function resetOtpUI() {
        els.otp.value = '';
        showOtpError('');
        els.verifyOtpBtn.disabled = false;
        els.otp.disabled = false;
        clearInterval(countdownTimer);
        countdownTimer = null;
    }

    function validateForm() {
        document.querySelectorAll('.client-error').forEach(e => e.remove());
        let ok = true;

        Object.entries(required).forEach(([id, msg]) => {
            const el = $(id);
            if (!el || !el.value.trim()) {
                const small = document.createElement('small');
                small.className = 'text-danger client-error';
                small.innerText = msg;
                el.closest('.form-group')?.appendChild(small);
                ok = false;
            }
        });

        const serviceSelect = $('service_id');
        if (serviceSelect && !serviceSelect.querySelector('option:checked')) {
            const small = document.createElement('small');
            small.className = 'text-danger client-error';
            small.innerText = 'Vui lòng chọn dịch vụ';
            serviceSelect.closest('.form-group')?.appendChild(small);
            ok = false;
        }

        return ok;
    }

    function sendOtp() {
        return post('/email/send-code', {
            email: els.email.value
        });
    }

    function verifyOtp(code) {
        return post('/email/verify-otp', {
            email: els.email.value,
            code
        });
    }

    function startCountdown() {
        let seconds = 60;

        if (countdownTimer) clearInterval(countdownTimer);

        els.countdownText.classList.remove('d-none');
        els.resendBtn.classList.add('d-none');
        els.countdown.textContent = seconds;

        countdownTimer = setInterval(() => {
            seconds--;
            els.countdown.textContent = seconds;

            if (seconds <= 0) {
                clearInterval(countdownTimer);
                countdownTimer = null;
                els.countdownText.classList.add('d-none');
                els.resendBtn.classList.remove('d-none');
            }
        }, 1000);
    }

    els.submitBtn.addEventListener('click', () => {
        if (verified || submitting) return;

        if (!validateForm()) return;

        submitting = true;
        showOtpError('');

        post('/email/reset-otp')
            .then(() => sendOtp())
            .then(() => {
                startCountdown();
                modal.show();
            })
            .catch(err => {
                showOtpError(err.message || 'Có lỗi xảy ra');
            })
            .finally(() => {
                submitting = false;
            });
    });

    els.verifyOtpBtn.addEventListener('click', () => {
        const code = els.otp.value.replace(/\D/g, '');

        if (code.length !== 6) {
            showOtpError('Vui lòng nhập đủ 6 chữ số');
            return;
        }

        els.verifyOtpBtn.disabled = true;
        els.otp.disabled = true;
        showOtpError('');

        verifyOtp(code)
            .then(() => {
                verified = true;
                modal.hide();

                return fetch(els.form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: new FormData(els.form)
                });
            })
            .then(async r => {
                const json = await r.json();
                if (!r.ok) throw new Error(json.message || 'Submit failed');
                window.showBookingSuccess(json);
            })
            .catch(err => {
                showOtpError(err.message || 'Mã không đúng hoặc đã hết hạn');
                els.verifyOtpBtn.disabled = false;
                els.otp.disabled = false;
            });
    });

    els.resendBtn.addEventListener('click', () => {
        if (!els.email.value) return;

        showOtpError('');
        els.resendBtn.disabled = true;

        sendOtp()
            .then(() => startCountdown())
            .catch(err => showOtpError(err.message || 'Có lỗi xảy ra'))
            .finally(() => {
                els.resendBtn.disabled = false;
            });
    });

    els.closeBtn.addEventListener('click', () => {
        modal.hide();
        resetOtpUI();
    });

    els.otp.addEventListener('input', () => {
        els.otp.value = els.otp.value.replace(/\D/g, '').slice(0, 6);
    });
});
