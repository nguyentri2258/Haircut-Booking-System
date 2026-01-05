document.addEventListener('DOMContentLoaded', () => { 
    const form = document.getElementById('bookingForm'); 
    const emailInput = document.getElementById('email'); 
    const modalEl = document.getElementById('emailVerificationModal'); 
    const submitBtn = document.getElementById('submit-btn'); 
    
    if (!form || !modalEl || !submitBtn) 
        return; 
    
    const modal = new bootstrap.Modal(modalEl); 
    
    let verified = false; 
    let submitting = false; 
    
    submitBtn.addEventListener('click', function (e) {
        if (verified) return;

        e.preventDefault();

        if (!form.checkValidity()) {
            form.reportValidity(); // hiện lỗi ngay dưới input
            return;
        }

        // 🔥 RESET STATE SERVER TRƯỚC KHI GỬI
        fetch('/email/reset-otp', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document
                    .querySelector('meta[name="csrf-token"]').content
            }
        }).finally(() => {
            sendOtp(emailInput.value);
            modal.show();
            startOtpCountdown();
        });
    });

    
    function sendOtp(email) {
        return fetch('/email/send-code', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document
                    .querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ email })
        })
        .then(async res => {
            const data = await res.json().catch(() => ({}));
            if (!res.ok) throw new Error(data.message);
            return data;
        });
    }

    
    window.bookingEmailVerified = function (code) { 
        fetch('/email/verify-otp', { 
            method: 'POST', 
            headers: { 'Content-Type': 'application/json', 
                'X-CSRF-TOKEN': document 
                .querySelector('meta[name="csrf-token"]').content 
            }, 
            body: JSON.stringify({ email: emailInput.value, code: code }) 
        }) 
        .then(res => { 
            if (!res.ok) throw new Error(); 
            return res.json(); 
        }) 
        .then(() => { 
            verified = true; 
            modal.hide(); 
            fetch(form.action, { 
                method: 'POST', 
                body: new FormData(form), 
                headers: { 
                    'X-CSRF-TOKEN': document 
                    .querySelector('meta[name="csrf-token"]').content, 
                    'Accept': 'application/json' 
                } 
            }) 
            .then(r => { 
                if (!r.ok) throw new Error(); 
                return r.json(); 
            }) 
            .then(data => { 
                if (!data.success) throw new Error();
                window.showBookingSuccess(data); 
            }) 
            .catch(() => { 
                alert('Có lỗi khi xác nhận hoặc đặt lịch'); 
            }) 
            .finally(() => { 
                submitting = false; 
            }); 
        }) 
        .catch(() => { 
            submitting = false; 
            alert('Mã xác nhận không đúng hoặc đã hết hạn'); 
        }); 
    }; 
    
    const otpInput = document.getElementById('otp-code'); 
    const verifyBtn = document.getElementById('verify-otp-btn'); 
    if (verifyBtn && otpInput) { 
        verifyBtn.addEventListener('click', () => { 
            const code = otpInput.value.trim(); 
            if (code.length !== 6) { 
                alert('Vui lòng nhập đủ 6 chữ số'); 
                return; 
            } 
            bookingEmailVerified(code); 
        }); 
    } 
    
    let otpTimer = null; 
    let otpSeconds = 60; 
    function startOtpCountdown() { 
        const countdownText = document.getElementById('otpCountdownText'); 
        const countdownEl = document.getElementById('otpCountdown'); 
        const resendBtn = document.getElementById('otpResendBtn'); 
        
        if (!countdownEl || !resendBtn) 
            return; 
        
        otpSeconds = 60; 
        countdownEl.textContent = otpSeconds; 
        countdownText.classList.remove('d-none'); 
        resendBtn.classList.add('d-none'); 
        
        if (otpTimer) 
            clearInterval(otpTimer); 
        
        otpTimer = setInterval(() => { 
            otpSeconds--; 
            countdownEl.textContent = otpSeconds; 
            if (otpSeconds <= 0) { 
                clearInterval(otpTimer); 
                countdownText.classList.add('d-none'); 
                resendBtn.classList.remove('d-none'); 
            } 
        }, 1000); 
    } 
    
    const resendBtn = document.getElementById('otpResendBtn'); 
    
    if (resendBtn) { 
        resendBtn.addEventListener('click', () => { 
            const email = emailInput.value; 
            if (!email) return; 
            sendOtp(email); 
            startOtpCountdown(); 
        }); 
    } 
    
    function resetOtpState() { 
        verified = false; 
        submitting = false; 

        const otpInput = document.getElementById('otp-code'); 
        const otpError = document.getElementById('otpError'); 
        
        if (otpInput) otpInput.value = ''; 
        if (otpError) otpError.classList.add('d-none'); 
        
        const resendBtn = document.getElementById('otpResendBtn'); 
        const countdownText = document.getElementById('otpCountdownText'); 

        if (resendBtn) resendBtn.classList.add('d-none'); 
        if (countdownText) countdownText.classList.remove('d-none'); 
        
        const modalEl = document.getElementById('emailVerificationModal'); 
        const modalInstance = bootstrap.Modal.getInstance(modalEl); 
        
        modalInstance?.hide(); 
    } 
    
    document.getElementById('closeOtpBtn')?.addEventListener('click', () => { 
        if (confirm('Bạn có chắc muốn huỷ xác nhận email?')) { 
            resetOtpState(); 
        } 
    }); 
});