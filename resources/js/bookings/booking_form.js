document.addEventListener('DOMContentLoaded', () => {
    const {
        oldUser,
        oldTime,
        routes
    } = window.BOOKING_CREATE;

    const addressSelect = document.getElementById('address_id');
    const dateInput     = document.getElementById('date');
    const userSelect    = document.getElementById('user_id');
    const timeSelect    = document.getElementById('time_of_day');
    const submitBtn     = document.getElementById('submit-btn');
    const container     = document.getElementById('userBoxContainer');

    let fp = null;

    const serviceSelect = document.getElementById('service_id');
    const totalDiv = document.getElementById('total-price');

    function updateTotal() {
        let total = 0;
        [...serviceSelect.selectedOptions].forEach(o => {
            total += Number(o.dataset.price || 0);
        });
        totalDiv.textContent = `Tổng: ${total.toLocaleString()}đ`;
    }

    serviceSelect.addEventListener('change', updateTotal);
    updateTotal();

    if (window.TomSelect) {
        new TomSelect('#service_id', { plugins: ['remove_button'] });
    }

    function setLoadingStylist() {
        container.innerHTML = '<div class="text-muted">Đang tải stylist...</div>';
        userSelect.innerHTML = `<option value="">Chọn thợ</option><option value="auto">Auto</option>`;
    }

    function selectStylist(id, box) {
        userSelect.value = id;
        container.querySelectorAll('.stylist-box')
            .forEach(b => b.classList.remove('stylist-selected'));
        if (box) box.classList.add('stylist-selected');
        userSelect.dispatchEvent(new Event('change'));
    }

    function renderStylistBoxes() {
        container.innerHTML = '';

        const auto = document.createElement('div');
        auto.className = 'stylist-box stylist-auto';
        auto.innerHTML = 'Để chúng tôi<br>chọn cho bạn';
        auto.onclick = () => selectStylist('auto', auto);
        container.appendChild(auto);

        [...userSelect.options].forEach(opt => {
            if (!opt.value || opt.value === 'auto') return;

            const box = document.createElement('div');
            box.className = 'stylist-box';
            box.innerHTML = `<img src="${opt.dataset.avatar}"><p>${opt.text}</p>`;
            box.onclick = () => selectStylist(opt.value, box);

            if (oldUser == opt.value) selectStylist(opt.value, box);
            container.appendChild(box);
        });
    }

    addressSelect.addEventListener('change', () => {

        const addressId = addressSelect.value;

        userSelect.value = '';
        timeSelect.innerHTML = '';
        dateInput.value = '';

        if (!addressId) return;

        setLoadingStylist();

        fetch(`${routes.stylists}?address_id=${addressId}`)
            .then(r => r.json())
            .then(users => {

                userSelect.innerHTML = `<option value="">Chọn thợ</option><option value="auto">Auto</option>`;

                users.forEach(u => {
                    const opt = document.createElement('option');
                    opt.value = u.id;
                    opt.text  = u.name;
                    opt.dataset.avatar = u.avatar;
                    userSelect.appendChild(opt);
                });

                renderStylistBoxes();
                loadHolidays(addressId);
            });
    });

    function loadHolidays(addressId) {

        if (fp) fp.destroy();

        fetch(`${routes.holidays}?address_id=${addressId}`)
            .then(r => r.json())
            .then(data => {
                fp = flatpickr(dateInput, {
                    dateFormat: 'Y-m-d',
                    locale: 'vn',
                    disable: [
                        ...data.holidays,
                        d => d < new Date().setHours(0,0,0,0)
                    ],
                    onChange() {
                        submitBtn.disabled = false;
                        dateInput.dispatchEvent(new Event('change'));
                    }
                });
            });
    }

    function loadTimes() {

        if (!dateInput.value || !userSelect.value || !addressSelect.value) return;

        timeSelect.innerHTML = '<option>Đang tải...</option>';

        fetch(`${routes.times}?` + new URLSearchParams({
            date: dateInput.value,
            user_id: userSelect.value,
            address_id: addressSelect.value
        }))
        .then(r => r.json())
        .then(data => {
            timeSelect.innerHTML = '<option value="">Chọn giờ</option>';

            data.forEach(item => {
                const opt = document.createElement('option');

                if (typeof item === 'object') {
                    opt.value = `${item.time}|${item.user_id}`;
                    opt.text  = `${item.time} - ${item.user_name}`;
                } else {
                    opt.value = item;
                    opt.text  = item;
                }

                timeSelect.appendChild(opt);
            });

            if (oldTime) timeSelect.value = oldTime;
        });
    }

    dateInput.addEventListener('change', loadTimes);
    userSelect.addEventListener('change', loadTimes);

    if (addressSelect.value) {
        addressSelect.dispatchEvent(new Event('change'));
    }

});