document.addEventListener('DOMContentLoaded', function () {
    const dateInput     = document.getElementById('date');
    const stylistSelect = document.getElementById('user_id');
    const addressSelect = document.getElementById('address_id');
    const timeSelect    = document.getElementById('time_of_day');
    const {
        bookingId,
        originalUserId,
        originalAddressId,
        oldTime,
        services,
        routes
    } = window.bookingData;

    async function loadStylists() {
        const addressId = addressSelect.value;
        if (!addressId) return;

        try {
            const res  = await fetch(`${routes.stylists}?address_id=${addressId}`);
            const data = await res.json();

            stylistSelect.innerHTML = `
                <option value="">Chọn thợ làm tóc</option>
                <option value="auto">Để chúng tôi chọn cho bạn</option>
            `;

            data.forEach(user => {
                const opt = document.createElement('option');
                opt.value = user.id;
                opt.textContent = user.name;
                stylistSelect.appendChild(opt);
            });

            if ([...stylistSelect.options].some(o => o.value == originalUserId)) {
                stylistSelect.value = originalUserId;
            }

            loadTimes(addressId == originalAddressId);

        } catch (e) {
            console.error('Load stylist lỗi:', e);
        }
    }

    function loadTimes(preserveOld = false) {
        const date      = dateInput.value;
        const userId    = stylistSelect.value;
        const addressId = addressSelect.value;

        if (!date || !userId || !addressId) {
            timeSelect.innerHTML = '<option value="">Chọn giờ</option>';
            return;
        }

        timeSelect.innerHTML = '<option>Đang tải...</option>';

        fetch(
            `${routes.times}?date=${date}&user_id=${userId}&address_id=${addressId}&booking_id=${bookingId}`
        )
            .then(res => res.json())
            .then(data => {
                timeSelect.innerHTML = '<option value="">Chọn giờ</option>';

                if (!data.length) {
                    timeSelect.innerHTML = '<option value="">Không có giờ trống</option>';
                    return;
                }

                const isAuto   = userId === 'auto';
                let foundOld   = false;

                data.forEach(item => {
                    const value = isAuto
                        ? `${item.time}|${item.user_id}`
                        : item;

                    let label = isAuto
                        ? `${item.time} - ${item.user_name}`
                        : item;

                    const opt = document.createElement('option');
                    opt.value = value;

                    if (preserveOld && value === oldTime) {
                        opt.selected = true;
                        label += ' (giờ cũ)';
                        foundOld = true;
                    }

                    opt.textContent = label;
                    timeSelect.appendChild(opt);
                });

                if (preserveOld && !foundOld) {
                    timeSelect.selectedIndex = 0;
                }
            });
    }

    function updateServiceTotal(ids) {
        const total = ids.reduce((sum, id) => {
            const s = services.find(x => x.id == id);
            return sum + (s ? s.price : 0);
        }, 0);

        document.getElementById('service-total').textContent =
            `Tổng: ${total.toLocaleString()}đ`;
    }

    const serviceSelect = new TomSelect('#service_id', {
        plugins: ['remove_button'],
        placeholder: 'Chọn dịch vụ',
        create: false,
        onChange: () => updateServiceTotal(serviceSelect.getValue())
    });

    updateServiceTotal(serviceSelect.getValue());

    addressSelect.addEventListener('change', loadStylists);
    stylistSelect.addEventListener('change', () => loadTimes());
    dateInput.addEventListener('change', () => loadTimes());

    if (addressSelect.value && dateInput.value) {
        loadStylists();
    }
});