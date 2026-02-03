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
        otpError: $('otpError')
    };

    if (!els.form || !els.modal || !els.submitBtn) return;

    const modal = new bootstrap.Modal(els.modal);

    let verified = false;
    let submitting = false;

    const required = {
        name: 'Vui lòng điền tên',
        phone: 'Vui lòng điền số điện thoại',
        email: 'Vui lòng điền email',
        address_id: 'Vui lòng chọn chi nhánh',
        service_id: 'Vui lòng chọn dịch vụ',
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

    function showOtpError(msg) {
        if (!els.otpError) return;
        if (!msg) {
            els.otpError.classList.add('d-none');
            els.otpError.textContent = '';
            return;
        }
        els.otpError.textContent = msg;
        els.otpError.classList.remove('d-none');
    }

    function validate() {
        document.querySelectorAll('.client-error').forEach(e => e.remove());
        let ok = true;

        Object.entries(required).forEach(([id, msg]) => {
            const el = $(id);
            if (!el || !el.value.trim()) {
                const small = document.createElement('small');
                small.className = 'text-danger client-error';
                small.innerText = msg;
                el.closest('.form-group').appendChild(small);
                ok = false;
            }
        });

        return ok;
    }

    els.submitBtn.addEventListener('click', () => {
        if (verified || submitting) return;

        if (!validate()) return;

        submitting = true;
        showOtpError('');

        post('/email/reset-otp')
            .then(() => sendOtp())
            .then(() => {
                startCountdown();
                modal.show();
            })
            .catch(err => showOtpError(err.message || 'Co loi xay ra'))
            .finally(() => { submitting = false; });
    });

    function sendOtp() {
        return post('/email/send-code', { email: els.email.value });
    }

    function verifyOtp(code) {
        return post('/email/verify-otp', {
            email: els.email.value,
            code
        });
    }

    window.bookingEmailVerified = code => {
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
            .then(r => r.json())
            .then(d => window.showBookingSuccess(d))
            .catch(err => showOtpError(err.message || 'Co loi xay ra'))
            .finally(() => submitting = false);
    };

    els.verifyOtpBtn?.addEventListener('click', () => {
        const code = els.otp.value.trim();
        if (code.length !== 6) return alert('Nhập đủ 6 số');
        bookingEmailVerified(code);
    });

    function startCountdown() {
        let s = 60;
        els.countdownText?.classList.remove('d-none');
        els.resendBtn?.classList.add('d-none');
        if (els.countdown) els.countdown.textContent = s;

        const timer = setInterval(() => {
            s--;
            els.countdown.textContent = s;

            if (s <= 0) {
                clearInterval(timer);
                els.countdownText?.classList.add('d-none');
                els.resendBtn?.classList.remove('d-none');
            }
        }, 1000);
    }

    els.resendBtn?.addEventListener('click', () => {
        if (!els.email.value) return;
        showOtpError('');
        sendOtp()
            .then(() => startCountdown())
            .catch(err => showOtpError(err.message || 'Có lỗi xảy ra'));
    });

    $('closeOtpBtn')?.addEventListener('click', () => modal.hide());
});

