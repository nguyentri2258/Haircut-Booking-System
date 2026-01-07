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
        countdownText: $('otpCountdownText')
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
        }).then(r => r.json());
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

        post('/email/reset-otp')
            .finally(() => {
                submitting = false;
                sendOtp();
                startCountdown();
                modal.show();
            });
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
            .catch(() => alert('Có lỗi xảy ra'))
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
        sendOtp();
        startCountdown();
    });

    $('closeOtpBtn')?.addEventListener('click', () => modal.hide());
});
